/**
 * Gestion d'une classe abstraite gérant un composant
 */
export default class Component {

	// component: null,
	// _loading: false,
	
	constructor(component, ready = true) {
		
		component.addClass('init');
		
		this.component = component;
		this._loading = false;
		
		if(ready) {
			this.onReady();
		}

	};
	
	onReady() {
		this.initEvents();
	};
	
	initEvents() {
		// A surcharger dans chaque classe
	};
	
	loading(loading) {
		
		if(loading) {
			this.showLoading();
		} else {
			this.hideLoading();
		}
		this._loading = loading;
	};
	
	showLoading() {
		// Méthode à surcharger dans les classes filles
	};
	
	hideLoading() {
		// Méthode à surcharger dans les classes filles
	};
};
