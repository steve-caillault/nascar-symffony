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

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();
        $this->getHeaderMenus()->add($this->user_menu);
    }

    /**
     * Renders a view.
     * @param string $view
     * @param array $parameters
     * @param Response
     * @return Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null) : Response
    {
        $this->fillHeaderMenus();
        return parent::render($view, $parameters, $response);
    }

}