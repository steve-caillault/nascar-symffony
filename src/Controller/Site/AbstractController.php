<?php

/**
 * Contrôleur de base du site
 */

namespace App\Controller\Site;

use App\Controller\AbstractController as BaseAbstractController;
use App\UI\Site\Menus\Header\MainMenu;

abstract class AbstractController extends BaseAbstractController
{

    /**
     * Menu principal du site
     * @var MainMenu
     */
    private MainMenu $mainMenu;

    /**
     * Initialisation du menu principal du site
     * @param MainMenu $mainMenu
     * @required
     * @return void
     */
    public function setMainMenu(MainMenu $mainMenu) : void
    {
        $this->mainMenu = $mainMenu;
    }

    /**
     * Remplit l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();
        $this->getHeaderMenus()->add($this->mainMenu);
    }

}