<?php

/**
 * Contrôleur abstrait pour le panneau d'administration
 */

namespace App\Controller\Admin;

use App\Controller\AbstractController as BaseAbstractController;
use App\UI\Menus\Breadcrumb\{
    Breadcrumb,
    BreadcrumbItem
};

abstract class AdminAbstractController extends BaseAbstractController implements AdminControllerInterface {

     /**
     * Initialise le fil d'ariane
     * @param Breadcrumb $breadcrumb
     * @return void
     * @required
     */
    public function setBreadcrumb(Breadcrumb $breadcrumb)
    {
        parent::setBreadcrumb($breadcrumb);

        $breadcrumb->addItem(new BreadcrumbItem('admin.label', 'admin.alt', 'app_admin_default_index'));

    }

}