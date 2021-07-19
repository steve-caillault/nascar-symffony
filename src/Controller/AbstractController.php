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

abstract class AbstractController extends SymfonyAbstractController
{

    /**
     * Fil d'ariane
     * @var Breadcrumb
     */
    private Breadcrumb $breadcrumb;

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

}