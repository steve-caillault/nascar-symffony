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
use App\Entity\Country;
use App\UI\Menus\Header\HeaderItemMenu;

final class CountriesMenu extends HeaderMenu {

    /**
     * Country géré
     * @var ?Country
     */
    private ?Country $country = null;

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
            $this->addCountryItem();
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
    private function addCountryItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un pays
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
                'countryCode' => $country->getCode(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}