<?php

/**
 * Gestion d'un élément d'un menu
 */

namespace App\UI\Menus\Header;

final class HeaderItemMenu {

    /**
     * Niveau de l'élément dans l'arborescence
     * @var int
     */
    private int $level = 1;

    /**
     * Texte du menu
     * @var string
     */
    private string $label;

    /**
     * Texte alternative pour l'ancre
     * @var ?string
     */
    private ?string $alt_label = null;

    /**
     * Nom de la route à utiliser
     * @var ?string
     */
    private ?string $route_name = null;

    /**
     * Paramètres de la route à utiliser
     * @var array
     */
    private array $route_parameters = [];

    /**
     * Classes CSS de l'élément 
     * @var array
     */
    private array $classes = [];

    /**
     * Liste des enfants
     * @var array
     */
    private array $children = [];

    /**
     * Modifie le texte du menu
     * @param string $label
     * @return self
     */
    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Modifie le texte alternatif de l'ancre
     * @param string $text
     * @return self
     */
    public function setAltLabel(string $text) : self
    {
        $this->alt_label = $text;
        return $this;
    }

    /**
     * Modifie le nom de la route de l'ancre à utiliser
     * @param string $routeName
     * @return self
     */
    public function setRouteName(string $routeName) : self
    {
        $this->route_name = $routeName;
        return $this;
    }

    /**
     * Modifier les paramètres de la route de l'ancre à utiliser
     * @param array $parameters
     * @return self
     */
    public function setRouteParameters(array $routeParameters) : self
    {
        $this->route_parameters = $routeParameters;
        return $this;
    }

    /**
     * Ajoute une classe CSS au menu
     * @param string $classarray default: []
     * @return self
     */
    public function addClass(string $class) : self
    {
        $this->classes[] = $class;
        return $this;
    }

    /**
     * Ajoute un enfant à l'élément courant
     * @param self $child
     * @return self
     */
    public function addChild(self $child) : self
    {
        $this->children[] = $child;
        $child->level = $this->level + 1;
        return $this;
    }

    /**
     * Retourne le niveau de l'élément dans l'arborescence
     * @return int
     */
    public function getLevel() : int
    {
        return $this->level;
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
     * Retourne le texte alternatif de l'ancre
     * @return ?string
     */
    public function getAltLabel() : ?string
    {
        return $this->alt_label;
    }

    /**
     * Retourne le nom de la route
     * @return string
     */
    public function getRouteName() : ?string
    {
        return $this->route_name;
    }

    /**
     * Retourne les paramètres de la route
     * @return array
     */
    public function getRouteParameters() : array
    {
        return $this->route_parameters;
    }

    /**
     * Retourne les classes CSS de l'élément
     * @return array
     */
    public function getClasses() : array
    {
        return $this->classes;
    }

    /**
     * Retourne les enfants
     * @return array
     */
    public function getChildren() : array
    {
        return $this->children;
    }
}