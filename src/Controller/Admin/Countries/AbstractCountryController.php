<?php

/**
 * Contrôleur de base pour la gestion des pays
 */

namespace App\Controller\Admin\Countries;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\CountriesMenu;
use App\Entity\Country;

abstract class AbstractCountryController extends AdminAbstractController {

    /**
     * Menu des pays
     * @var CountriesMenu
     */
    private CountriesMenu $countries_menu;

    /**
     * Pays géré
     * @var ?Country
     */
    private ?Country $country = null;

    /**
     * Initialise le menu des pays
     * @param CountriesMenu
     * @return void
     * @required
     */
    public function setCountriesMenu(CountriesMenu $countriesMenu) : void
    {
        $this->countries_menu = $countriesMenu;
    }

    /**
     * Modifie le pays à gérer
     * @param Country $country
     * @return self
     */
    protected function setCountry(Country $country) : self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->country !== null)
        {
            $this->countries_menu->setCountry($this->country);
        }

        $this->getHeaderMenus()->addAfter($this->countries_menu, $this->getAdminMenu());
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();

        $breadcrumb = $this->getBreadcrumb();
        
        $breadcrumb->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.countries.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.countries.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_countries_list_index'
        ));

        if($this->country !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $this->country->getName(),
                altLabel: $this->translator->trans('admin.country.edit.alt_label', [
                    'name' => $this->country->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_country_edit_index',
                routeParameters: [
                    'countryCode' => $this->country->getCode(),
                ],
            ));
        }
    }

}