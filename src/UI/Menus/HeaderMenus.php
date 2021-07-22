<?php

/**
 * Gestion des menus de l'en-tête du site
 */

namespace App\UI\Menus;

final class HeaderMenus {

    /**
     * Liste des menus
     * @var array
     */
    private array $menus = [];

    /**
     * Retourne la liste des menus
     * @return array
     */
    public function get() : array
    {
        return $this->menus;
    }

}