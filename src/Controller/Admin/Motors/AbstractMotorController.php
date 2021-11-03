<?php

/**
 * Contrôleur de base pour la gestion des moteurs
 */

namespace App\Controller\Admin\Motors;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\MotorsMenu;
use App\Entity\Motor;

abstract class AbstractMotorController extends AdminAbstractController {

    /**
     * Menu des moteurs
     * @var MotorsMenu
     */
    private MotorsMenu $motors_menu;

    /**
     * Moteur géré
     * @var ?Motor
     */
    private ?Motor $motor = null;

    /**
     * Initialise le menu des moteurs
     * @param MotorsMenu
     * @return void
     * @required
     */
    public function setMotorsMenu(MotorsMenu $motorsMenu) : void
    {
        $this->motors_menu = $motorsMenu;
    }

    /**
     * Modifie le moteur à gérer
     * @param Motor $motor
     * @return self
     */
    protected function setMotor(Motor $motor) : self
    {
        $this->motor = $motor;
        return $this;
    }

    /**
     * Retourne le moteur à gérer
     * @return ?Motor
     */
    protected function getMotor() : ?Motor
    {
        return $this->motor;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->motor !== null)
        {
            $this->motors_menu->setMotor($this->motor);
        }

        $this->getHeaderMenus()->addAfter($this->motors_menu, $this->getAdminMenu());
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
            label: $this->translator->trans('admin.motors.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.motors.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_motors_list_index'
        ));

        $motor = $this->motor;
        if($motor !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $motor->getName(),
                altLabel: $this->translator->trans('admin.motors.edit.alt_label', [
                    'name' => $motor->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_motors_edit_index',
                routeParameters: [
                    'motorPublicId' => strtolower($this->motor->getPublicId()),
                ],
            ));
        }
    }

}