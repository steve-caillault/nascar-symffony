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
use App\UI\Admin\Menus\Header\UserMenu;
use Symfony\Component\HttpFoundation\Response;

abstract class AdminAbstractController extends BaseAbstractController implements AdminControllerInterface {

    /**
     * Menu de l'utilisateur
     * @var UserMenu
     */
    private ?UserMenu $user_menu = null;

    /**
     * Initialise le menu de l'utilisateur
     * @param UserMenu
     * @return void
     * @required
     */
    public function setUserMenu(UserMenu $userMenu)
    {
       $this->user_menu = $userMenu;
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem('admin.label', 'admin.alt', 'app_admin_default_index'));
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();
        $this->getHeaderMenus()->add($this->user_menu);
    }

}