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
        if(! $withItems)
        {
            return $this;
        }
        
        $this->menus[] = $headerMenu;
        
        return $this;
    }

    /**
     * Ajoute un menu après un autre menu
     * @param HeaderMenu $menu Le menu à ajouter
     * @param HeaderMenu $neighborMenu Le menu après lequel il faut faire l'ajout
     * @return self
     */
    public function addAfter(HeaderMenu $menu, HeaderMenu $neighborMenu) : self
    {
        $withItems = (count($menu->getItems()) > 0);
        if(! $withItems)
        {
            return $this;
        }

        $position = array_search($neighborMenu, $this->menus);
        if($position !== false)
        {
            $before = array_slice($this->menus, 0, $position);
            $after = array_slice($this->menus, $position + 1);
            
            $this->menus = [
                ...$before,
                ...[ $neighborMenu, $menu, ],
                ...$after,
            ];
            
        }

        return $this;
    }  

}