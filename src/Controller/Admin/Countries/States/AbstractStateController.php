<?php

/**
 * Contrôleur de base pour la gestion des états d'un pays
 */

namespace App\Controller\Admin\Countries\States;

use App\Controller\Admin\Countries\AbstractCountryController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Entity\CountryState;

abstract class AbstractStateController extends AbstractCountryController {

    /**
     * Etat géré
     * @var ?CountryState
     */
    private ?CountryState $country_state = null;

    /**
     * Modifie l'état à gérer
     * @param CountryState $countryState
     * @return self
     */
    protected function setCountryState(CountryState $countryState) : self
    {
        $this->country_state = $countryState;
        return $this;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        if($this->country_state !== null)
        {
            $this->getCountriesMenu()->setCountryState($this->country_state);
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

        $countryCode = strtolower($this->getCountry()->getCode());
        
        $breadcrumb->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.countries.states.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.countries.states.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_countries_states_list_index',
            routeParameters: [
                'countryCode' => $countryCode,
            ]
        ));

        if($this->country_state !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $this->country_state->getName(),
                altLabel: $this->translator->trans('admin.country.states.edit.alt_label', [
                    'name' => $this->country_state->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_countries_states_edit_index',
                routeParameters: [
                    'countryStateCode' => $this->country_state->getCode(),
                    'countryCode' => $countryCode,
                ],
            ));
        }
    }

}