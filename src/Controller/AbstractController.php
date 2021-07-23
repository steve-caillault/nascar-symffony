<?php

/**
 * Contrôleur de base
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
/***/
use App\UI\Menus\Breadcrumb\{
    Breadcrumb, 
    BreadcrumbItem,
};
use App\UI\Menus\Header\HeaderMenus;

abstract class AbstractController extends SymfonyAbstractController
{

    /**
     * Fil d'ariane
     * @var Breadcrumb
     */
    private Breadcrumb $breadcrumb;

    /**
     * Menus de l'en-tête
     * @var HeaderMenus
     */
    private HeaderMenus $header_menus;

    /************************************************************/

    /**
     * Initialise le fil d'ariane
     * @param Breadcrumb $breadcrumb
     * @return void
     * @required
     */
    public function setBreadcrumb(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb->addItem(new BreadcrumbItem('home.label', 'home.alt', 'app_default_index'));
    }

    /**
     * Retourne le fil d'ariane
     * @return Breadcrumb
     */
    public function getBreadcrumb() : Breadcrumb
    {
        return $this->breadcrumb;
    }

    /************************************************************/

    /**
     * Initialise la liste des menus de l'en-tête
     * @param HeaderMenus
     * @return void
     * @required
     */
    public function setHeaderMenus(HeaderMenus $headerMenus) : void
    {
        $this->header_menus = $headerMenus;
    }

    /**
     * Retourne les menus de l'en-tête
     * @return HeaderMenus
     */
    protected function getHeaderMenus() : HeaderMenus
    {
        return $this->header_menus;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        
    }

    /************************************************************/

}