<?php

/**
 * Menu de la section Pays
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\Entity\{
    Country,
    CountryState,
    City
};
use App\UI\Menus\Header\HeaderItemMenu;

final class CountriesMenu extends HeaderMenu {

    /**
     * Pays géré
     * @var ?Country
     */
    private ?Country $country = null;

    /**
     * Etat géré
     * @var ?CountryState
     */
    private ?CountryState $countryState = null;

    /**
     * Ville gérée
     * @var ?City
     */
    private ?City $city = null;

    /**
     * Constructeur
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        
    }

    /**
     * Retourne la route courante
     * @return ?string
     */
    private function getCurrentRouteName() : ?string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        return $currentRequest?->attributes->get('_route');
    }

    /**
     * Modifie le pays à gérer
     * @param Country $country
     * @return self
     */
    public function setCountry(Country $country) : self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Modifie l'état à gérer
     * @param CountryState $countryState
     * @return self
     */
    public function setCountryState(CountryState $countryState) : self
    {
        $this->countryState = $countryState;
        return $this;
    }

    /**
     * Modifie la ville à gérer
     * @param City $city
     * @return self
     */
    public function setCity(City $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-countries-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des pays
        $this->addListItem();

        // Ajout d'un pays
        $this->addCreationItem();

        if($this->country !== null)
        {
            $this->addCountryItems();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des pays
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_countries_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un pays
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_countries_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un pays
     * @return self
     */
    private function addCountryItems() : self
    {
        $this->addEditItem();
        $this->addStatesListItem();
        $this->addStateCreationItem();

        if($this->countryState !== null)
        {
            $this->addCountryStateItems();
        }

        return $this;
    }

    /**
     * Ajoute les éléments de gestion d'un état
     * @return self
     */
    private function addCountryStateItems() : self
    {
        $this->addEditStateItem();
        $this->addCitiesListItem();
        $this->addCityCreationItem();
        if($this->city !== null)
        {
            $this->addEditCityItem();
        }
        return $this;
    }

    /**
     * Ajoute l'élément d'édition d'un pays
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $country = $this->country;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.edit.alt_label', [
                'name' => $country->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_edit_index')
            ->setRouteParameters([
                'countryCode' => strtolower($country->getCode()),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément d'édition de l'état
     * @return self
     */
    private function addEditStateItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $countryState = $this->countryState;
        $country = $this->country;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.edit.alt_label', [
                'name' => $countryState->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_edit_index')
            ->setRouteParameters([
                'countryStateCode' => strtolower($countryState->getCode()),
                'countryCode' => strtolower($country->getCode()),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de la liste des états du pays
     * @return self
     */
    private function addStatesListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.list.alt_label', [
                'name' => $this->country->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_list_index')
            ->setRouteParameters([
                'countryCode' => strtolower($this->country->getCode()),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajout l'élément de l'ajout d'un état à un pays
     * @return self
     */
    private function addStateCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.add.alt_label', [
                'name' => $this->country->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_add_index')
            ->setRouteParameters([
                'countryCode' => strtolower($this->country->getCode()),
            ]);

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de la liste des villes de l'état
     * @return self
     */
    private function addCitiesListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.cities.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.cities.list.alt_label', [
                'name' => $this->countryState->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_cities_list_index')
            ->setRouteParameters([
                'countryCode' => strtolower($this->country->getCode()),
                'countryStateCode' => strtolower($this->countryState->getCode()),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de création d'une ville
     * @return self
     */
    private function addCityCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.cities.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.cities.add.alt_label', [
                'name' => $this->countryState->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_cities_add_index')
            ->setRouteParameters([
                'countryCode' => strtolower($this->country->getCode()),
                'countryStateCode' => strtolower($this->countryState->getCode()),
            ]);

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'édition d'une ville
     * @return self
     */
    private function addEditCityItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.countries.states.cities.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.countries.states.cities.edit.alt_label', [
                'name' => $this->city->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_countries_states_cities_edit_index')
            ->setRouteParameters([
                'countryStateCode' => strtolower($this->countryState->getCode()),
                'countryCode' => strtolower($this->country->getCode()),
                'cityId' => $this->city->getId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }
}