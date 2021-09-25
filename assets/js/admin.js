import RootJS from 'App/root.js';
import AutocompleteInputFactory from 'App/Classes/Autocomplete.js';

window.initRootJS = () => {


    RootJS.Element.searchOne('html').addClass('javascript-enabled');

	// Gestion des champs d'autocomplètion
	RootJS.Element.searchList('div.autocomplete-search:not(.init)').forEach((element) => {
        AutocompleteInputFactory.get(element);
	});
};

document.addEventListener("DOMContentLoaded", () => { 
	(new RootJS.Controller()).execute();
}, false);