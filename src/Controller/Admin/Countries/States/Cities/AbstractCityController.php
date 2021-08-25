<?php

/**
 * Contrôleur de base pour la gestion des villes d'un état
 */

namespace App\Controller\Admin\Countries\States\Cities;

use App\Controller\Admin\Countries\States\AbstractStateController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Entity\City;

abstract class AbstractCityController extends AbstractStateController {

    /**
     * Ville gérée
     * @var ?City
     */
    private ?City $city = null;

    /**
     * Modifie la ville à gérer
     * @param City $city
     * @return self
     */
    protected function setCity(City $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Retourne la ville à gérer
     * @return ?City
     */
    protected function getCity() : ?City
    {
        return $this->city;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        if($this->city !== null)
        {
            $this->getCountriesMenu()->setCity($this->city);
        }

        parent::fillHeaderMenus();
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();

        $breadcrumb = $this->getBreadcrumb();

        $countryState = $this->getCountryState();
        $countryStateCode = strtolower($countryState->getCode());
        $countryCode = strtolower($this->getCountry()->getCode());
        
        // Ajout de l'élément vers la liste des villes
        $breadcrumb->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.countries.states.cities.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.countries.states.cities.alt_label', [
                'name' => $countryState->getName(),
            ], domain: 'breadcrumb'),
            routeName: 'app_admin_countries_states_cities_list_index',
            routeParameters: [
                'countryCode' => $countryCode,
                'countryStateCode' => $countryStateCode,
            ]
        ));
    }

}