<?php

/**
 * Contrôleur de base pour la gestion des saisons
 */

namespace App\Controller\Admin;

use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\SeasonsMenu;

abstract class AbstractSeasonsController extends AdminAbstractController {

    /**
     * Menu des saisons
     * @var SeasonsMenu
     */
    private SeasonsMenu $seasons_menu;

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
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        $this->getHeaderMenus()->addAfter($this->seasons_menu, $this->getAdminMenu());
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            label: 'admin.seasons.label',
            altLabel: 'admin.seasons.alt',
            routeName: 'app_admin_seasons_index'
        ));
    }

}