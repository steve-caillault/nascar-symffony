<?php

/**
 * Gestion des menus de l'en-tête du site
 */

namespace App\UI\Menus\Header;

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

    /**
     * Ajoute le menu
     * @param HeaderMenu
     * @return self
     */
    public function add(HeaderMenu $headerMenu) : self
    {
        $withItems = (count($headerMenu->getItems()) > 0);
        if($withItems)
        {
            $this->menus[] = $headerMenu;
        }
        
        return $this;
    }

}