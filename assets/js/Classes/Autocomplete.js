import RootJS from 'App/root.js';
import Component from 'App/Classes/Component.js';

/**
 * Instanciation d'un champs d'autocomplètion
 */
export default class AutocompleteInputFactory  {
	
	static get(element) {
		let type = element.getProperty('data-autocomplete-type'); 
	
		switch(type) {
			case 'ajax':
				return new AjaxAutocompleteInput(element);
			case 'data':
				return new DataAutocompleteInput(element);
		}
	}
	
};

/**
 * Classe abstraite pour un champs d'autocomplètion
 */
class AutocompleteInput extends Component {
	
	// inputs: {"text" : null, "hidden": null} // Liste des champs nécessaires
	// choices: null // Element HTML avec la liste des suggestions
	
	constructor(component) {
		super(component, false);
	
		this.choices = null;
		this.inputs = {
			"search": this.component.getChild('input.autocomplete-text'),
			"value": this.component.getChild('input.autocomplete-value')
		};
		
		this.onReady();
	};
	
	initEvents() {
		
		let self = this,
			inputSearch = this.inputs['search']
		;
		
		// Lorsqu'un caractère est ajouté ou supprimé
		inputSearch.addEvent('input', function(event) {
			let value = this.value,
				length = value.length
			;
			
			if(length == 0) { // Si on vide le champs on vide l'identifiant pré-sélectionné
				self.inputs['value'].setProperty('value', '');
				// self.component.fireEvent('updateValue');
			} else if(length > 2 && ! self._loading) { // Sinon on effectut la recherche
				self.loading(true);
				self.deleteChoices();
				self.request(value);
			}
		});
		
		// Navigation
		inputSearch.addEvent('keyup', function(event) {
			var key = event.key,
				navigationKeys = [ 'ArrowUp', 'ArrowDown' ],
				isNavigationKey = (navigationKeys.indexOf(key) != -1)
			;
			
			if(key == 'ArrowRight') { // Touche droite : sélection du choix
				var choice = (self.choices != null) ? self.choices.getChild('li.target') : null;
				if(choice != null) {
					choice.fireEvent('select');
				}
			} else if(isNavigationKey) { // Gestion de la navigation avec les flèches haut et bas
				if(self.choices != null) {
					self.navigation(key);
				}
			}
		});
		
		// Lorsqu'on perd le flux
		inputSearch.addEvent('blur', function(event) {
			if(self.choices != null) {
				self.choices.addClass('hide');
				setTimeout(function() {
					self.deleteChoices();
				}, 500);
			}
			
		});
		
	};
	
	/**
	 * Affiche le chargement
	 * @return void
	 */
	showLoading() {
		this.component.addClass('loading');
	};
	
	/**
	 * Cache le chargement
	 * @return void
	 */
	hideLoading() {
		this.component.removeClass('loading');
	};
	
	/**
	 * Requête et affichage des résultats
	 * @return void
	 */
	request() {
		throw 'Méthode request à surcharger.';
	};
	
	/**
	 * Affichage des choix dont les données dont en paramètre
	 * @param array data Données des choix à afficher
	 * @return void
	 */
	showChoices(data) {
	
		let self = this,
			choices = new RootJS.Element('ul', {
				'class': 'autocomplete-choices hide',
			})
		;
					
		data.forEach(function(choiceData) {
			let choice = new RootJS.Element('li', {
				'class': 'autocomplete-choice',
				'data-value': choiceData.value,
				'text': choiceData.text,
			});
			self.initChoice(choice);
			choices.addElement(choice);
		});
		self.choices = choices;
		self.component.addElement(choices);
		setTimeout(function() {
			choices.removeClass('hide');
		}, 500);
		
	};
	
	/**
	 * Initialisation du choix en paramètre
	 * @param ElementHTML choice
	 * @return void
	 */
	initChoice(choice) {
		
		var self = this;
		// A la sélection d'un choix
		choice.addEvent('select', function(event) {
			var element = RootJS.Element.retrieve(this),
				value = element.getProperty('data-value'),
				text = element.getProperty('text')
			;
			self.inputs['value'].setProperty('value', value);
			self.inputs['search'].setProperty('value', text);
			// self.component.fireEvent('selectElement', [ choice ]);
			self.component.fireEvent('updateValue');
			self.choices.addClass('hide');
			setTimeout(function() {
				self.deleteChoices();
			}, 500);
		});
		
		// Clic sur le choix
		choice.addEvent('click', function() {
			RootJS.Element.retrieve(this).fireEvent('select');
		});
		
		// Survole du choix
		choice.addEvent('mouseover', function() {
		
			let element = RootJS.Element.retrieve(this),
				choices = element.getParent('ul').getChildren('li')
			;
			
			choices.forEach(function(el) {
				el.removeClass('target');
			});
			
			element.addClass('target');
		});
	};
	
	/**
	 * Suppression des choix
	 * @return void
	 */
	deleteChoices() {
		let choices = this.choices;
		if(choices) {
			this.choices = null;
			choices.remove();
		}
	};
	
	/**
	 * Gestion de la navigation
	 */
	navigation(key) {

		var choices = this.choices.getChildren('li'),
			currentTarget = this.choices.getChild('li.target'),
			nextTarget = null
		;
			
		// Il n'y a pas assez d'éléments pour naviguer
		if(choices.length < 1) {
			return;
		}

		if(currentTarget != null) {
			currentTarget.removeClass('target');
			if(key == 'ArrowUp') {
				nextTarget = currentTarget.getPrevious('li');
			} else {
				nextTarget = currentTarget.getNext('li');
			}
		} 
		
		if(nextTarget == null) {
			if(key == 'ArrowUp') { // On met le dernier élément en subrillance
				nextTarget = this.choices.getLast('li');
			} else { // On met le premier élément en subrillance
				nextTarget = this.choices.getChild('li');
			}
		}
		
		nextTarget.addClass('target');
	};
};

/**
 * Gestion d'un champs d'autocomplètion dont les données sont chargés en Ajax
 */
class AjaxAutocompleteInput extends AutocompleteInput {

	/**
	 * Requête et affichage des résultats
	 * @return void
	 */
	request() {
	
		const request = new RootJS.JsonAjaxRequest({
			"url": this.component.getProperty('data-url'),
			"method": "post",
			"params": {
                "class": this.inputs['search'].getProperty('data-model'),
				"searching": this.inputs['search'].getProperty('value')
			}
		});

        request.execute().then((response) => {
            if(response && response.data && response.data.length > 0) {
                this.showChoices(response.data);
            }
        }).finally(() => {
            this.loading(false);
        })
		
	};

};

/**
 * Gestion d'un champs d'autocomplètion dont les données sont transmises en paramètre
 */
 class DataAutocompleteInput extends AutocompleteInput {
 	
 	// data: null, // Données où effectuer la recherche
 	
 	constructor(element) {
		super(element);
		this.data = [];
	};
 	
 	/**
 	 * Modifit les données où effectuer la recherche
 	 * @param array data
 	 * @return void
 	 */
 	setData(data) {
 		
		this.data = data;
 	};
 	
 	/**
	 * Requête et affichage des résultats
	 * @return void
	 */
	request() {
	
		let self = this,
			search = this.inputs['search'].getProperty('value'),
			choices = []
		;
	
		choices = this.data.filter(function(value) {
			let valueLower = value.toLowerCase(),
				searchLower = search.toLowerCase()
			;
			return (valueLower.indexOf(searchLower) != -1)
		}).slice(0, 10);
		
		this.loading(false);
		
		if(choices.length > 0) {
			this.showChoices(choices);
		}
	};
 	
 };
 
 
 