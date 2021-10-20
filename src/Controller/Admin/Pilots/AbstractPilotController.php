<?php

/**
 * Contrôleur de base pour la gestion des pilotes
 */

namespace App\Controller\Admin\Pilots;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\PilotsMenu;
use App\Entity\Pilot;

abstract class AbstractPilotController extends AdminAbstractController {

    /**
     * Menu des pilotes
     * @var PilotsMenu
     */
    private PilotsMenu $pilots_menu;

    /**
     * Pilote géré
     * @var ?Pilot
     */
    private ?Pilot $pilot = null;

    /**
     * Initialise le menu des pilotes
     * @param PilotsMenu
     * @return void
     * @required
     */
    public function setPilotsMenu(PilotsMenu $pilotsMenu) : void
    {
        $this->pilots_menu = $pilotsMenu;
    }

    /**
     * Modifie le pilote à gérer
     * @param Pilot $pilot
     * @return self
     */
    protected function setPilot(Pilot $pilot) : self
    {
        $this->pilot = $pilot;
        return $this;
    }

    /**
     * Retourne le pilote à gérer
     * @return ?Pilot
     */
    protected function getPilot() : ?Pilot
    {
        return $this->pilot;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->pilot !== null)
        {
            $this->pilots_menu->setPilot($this->pilot);
        }

        $this->getHeaderMenus()->addAfter($this->pilots_menu, $this->getAdminMenu());
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();

        $breadcrumb = $this->getBreadcrumb();
        
        $breadcrumb->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.pilots.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.pilots.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_pilots_list_index'
        ));

        $pilot = $this->pilot;
        if($pilot !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $pilot->getFullName(),
                altLabel: $this->translator->trans('admin.pilots.edit.alt_label', [
                    'name' => $pilot->getFullName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_pilots_edit_index',
                routeParameters: [
                    'pilotPublicId' => strtolower($this->pilot->getPublicId()),
                ],
            ));
        }
    }

}