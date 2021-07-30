<?php

/**
 * Contrôleur de base pour la gestion des saisons
 */

namespace App\Controller\Admin;

use App\UI\Menus\Breadcrumb\BreadcrumbItem;

abstract class AbstractSeasonsController extends AdminAbstractController {

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