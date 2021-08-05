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
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRouteName = $currentRequest?->attributes->get('_route');

        // Liste des saisons
        $listItem = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.seasons.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.seasons.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_seasons_index');
        if($currentRouteName === $listItem->getRouteName())
        {
            $listItem->addClass('selected');
        }

        // Ajout d'une saison
        $addSeasonItem = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.seasons.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.seasons.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_season_add_index');
        if($currentRouteName === $addSeasonItem->getRouteName())
        {
            $addSeasonItem->addClass('selected');
        }

        $this->addItems([
            $listItem,
            $addSeasonItem,
        ]);
    }

}