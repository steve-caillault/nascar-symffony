<?php

/**
 * Gestion d'un élément du fil d'ariane
 */

namespace App\UI\Menus\Breadcrumb;

final class BreadcrumbItem {

    /**
     * Constructeur
     * @param string $label Texte de l'élément
     * @param string $altLabel Texte alternatif de l'élément
     * @param ?string $routeName Nom de la route de l'élément
     * @param array $routeParameters Paramètres de la route de l'élément
     */
    public function __construct(
        private string $label, 
        private string $altLabel, 
        private ?string $routeName = null, 
        private array $routeParameters = []
    )
    {

    }

    /**
     * Retourne le texte de l'élément
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * Retourne le texte alternatif de l'élément
     * @return string
     */
    public function getAltLabel() : string
    {
        return $this->altLabel;
    }

    /**
     * Retourne le nom de la route de l'élément
     * @return ?string
     */
    public function getRouteName() : ?string
    {
        return $this->routeName;
    }

    /**
     * Route les paramètres de la route de l'élément
     * @return array
     */
    public function getRouteParameters() : array
    {
        return $this->routeParameters;
    }

}