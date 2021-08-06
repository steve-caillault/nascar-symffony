<?php

/**
 * Contrôleur abstrait pour le panneau d'administration
 */

namespace App\Controller\Admin;

use App\Controller\AbstractController as BaseAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\{
    UserMenu,
    AdminMenu
};

abstract class AdminAbstractController extends BaseAbstractController implements AdminControllerInterface {

    /**
     * Menu du panneau d'administration
     * @var AdminMenu
     */
    private AdminMenu $admin_menu;

    /**
     * Menu de l'utilisateur
     * @var UserMenu
     */
    private UserMenu $user_menu;

    /**
     * Initialise le menu du panneau d'administration
     * @param AdminMenu
     * @return void
     * @required
     */
    public function setAdminMenu(AdminMenu $adminMenu)
    {
        $this->admin_menu = $adminMenu;
    }

    /**
     * Retourne le menu du panneau d'administration
     * @return AdminMenu
     */
    public function getAdminMenu() : AdminMenu
    {
        return $this->admin_menu;
    }

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
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            $this->translator->trans('admin.label', domain: 'breadcrumb'), 
            $this->translator->trans('admin.alt_label', domain: 'breadcrumb'),
            'app_admin_default_index'
        ));
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->getUser() === null)
        {
            return;
        }
        
        $this->getHeaderMenus()
            ->add($this->admin_menu)
            ->add($this->user_menu)
        ;
    }

}