<?php

/**
 * Classe appelé par Twig pour le rendu des menus
 * Utilisée pour optimiser Twig à cause de l'injection de dépendance
 * @see https://symfony.com/doc/current/templating/twig_extension.html
 */

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;
/***/
use App\UI\Menus\Breadcrumb\{
    Breadcrumb,
    BreadcrumbService
};

final class MenusRuntime implements RuntimeExtensionInterface
{
    /**
     * Constructeur
     * @param BreadcrumbService $breadcrumbService
     */
    public function __construct(private BreadcrumbService $breadcrumbService)
    {

    }

    /**
     * Retourne le rendu du fil d'Ariane
     * @param Breadcrumb $breadcrumb
     * @return ?string
     */
    public function getBreadcrumbRender(Breadcrumb $breadcrumb) : ?string
    {
        return $this->breadcrumbService->getRender($breadcrumb);
    }

}