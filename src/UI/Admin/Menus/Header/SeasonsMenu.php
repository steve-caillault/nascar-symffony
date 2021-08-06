<?php

/**
 * Menu de la section Saisons
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\Entity\Season;
use App\UI\Menus\Header\HeaderItemMenu;

final class SeasonsMenu extends HeaderMenu {

    /**
     * Saison gérée
     * @var ?Season
     */
    private ?Season $season = null;

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
     * Modifie la saison à gérer
     * @param Season $season
     * @return self
     */
    public function setSeason(Season $season) : self
    {
        $this->season = $season;
        return $this;
    }

    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-seasons-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des saisons
        $this->addListItem();

        // Ajout d'une saison
        $this->addCreationItem();

        if($this->season !== null)
        {
            $this->addSeasonsItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des saisons
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.seasons.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.seasons.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_seasons_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'une saison
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.seasons.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.seasons.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_season_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'une saison
     * @return self
     */
    private function addSeasonsItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'une saison
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $season = $this->season;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.seasons.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.seasons.edit.alt_label', [
                'year' => $season->getYear(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_season_edit_index')
            ->setRouteParameters([
                'seasonYear' => $season->getYear(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}