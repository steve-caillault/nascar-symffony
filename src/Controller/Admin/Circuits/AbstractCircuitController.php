<?php

/**
 * Contrôleur de base pour la gestion des circuits
 */

namespace App\Controller\Admin\Circuits;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\CircuitsMenu;
use App\Entity\Circuit;

abstract class AbstractCircuitController extends AdminAbstractController {

    /**
     * Menu des circuits
     * @var CircuitsMenu
     */
    private CircuitsMenu $circuits_menu;

    /**
     * Circuit géré
     * @var ?Circuit
     */
    private ?Circuit $circuit = null;

    /**
     * Initialise le menu des circuits
     * @param CircuitsMenu
     * @return void
     * @required
     */
    public function setCircuitsMenu(CircuitsMenu $circuitsMenu) : void
    {
        $this->circuits_menu = $circuitsMenu;
    }

    /**
     * Modifie le circuit à gérer
     * @param Circuit $circuit
     * @return self
     */
    protected function setCircuit(Circuit $circuit) : self
    {
        $this->circuit = $circuit;
        return $this;
    }

    /**
     * Retourne le circuit à gérer
     * @return ?Circuit
     */
    protected function getCircuit() : ?Circuit
    {
        return $this->circuit;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->circuit !== null)
        {
            $this->circuits_menu->setCircuit($this->circuit);
        }

        $this->getHeaderMenus()->addAfter($this->circuits_menu, $this->getAdminMenu());
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
            label: $this->translator->trans('admin.circuits.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.circuits.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_circuits_list_index'
        ));

        $circuit = $this->circuit;
        if($circuit !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $circuit->getName(),
                altLabel: $this->translator->trans('admin.circuits.edit.alt_label', [
                    'name' => $circuit->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_circuits_edit_index',
                routeParameters: [
                    'circuitId' => strtolower($this->circuit->getId()),
                ],
            ));
        }
    }

}