<?php

/**
 * Contrôleur de base pour la gestion des saisons
 */

namespace App\Controller\Admin;

use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\SeasonsMenu;
use App\Entity\Season;

abstract class AbstractSeasonsController extends AdminAbstractController {

    /**
     * Menu des saisons
     * @var SeasonsMenu
     */
    private SeasonsMenu $seasons_menu;

    /**
     * Saison gérée
     * @var ?Season
     */
    private ?Season $season = null;

    /**
     * Initialise le menu des saisons
     * @param SeasonsMenu
     * @return void
     * @required
     */
    public function setSeasonsMenu(SeasonsMenu $seasonsMenu) : void
    {
        $this->seasons_menu = $seasonsMenu;
    }

    /**
     * Modifie la saison à gérer
     * @param Season $season
     * @return self
     */
    protected function setSeason(Season $season) : self
    {
        $this->season = $season;
        return $this;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->season !== null)
        {
            $this->seasons_menu->setSeason($this->season);
        }

        $this->getHeaderMenus()->addAfter($this->seasons_menu, $this->getAdminMenu());
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
            label: $this->translator->trans('admin.seasons.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.seasons.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_seasons_index'
        ));

        if($this->season !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $this->season->getYear(),
                altLabel: $this->translator->trans('admin.seasons.edit.alt_label', domain: 'breadcrumb'),
                routeName: 'app_admin_season_edit_index',
                routeParameters: [
                    'seasonYear' => $this->season->getYear(),
                ],
            ));
        }
    }

}