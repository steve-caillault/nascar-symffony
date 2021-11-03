<?php

/**
 * Menu de la section Moteurs
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\UI\Menus\Header\HeaderItemMenu;
use App\Entity\Motor;

final class MotorsMenu extends HeaderMenu {

    /**
     * Moteur à gérer
     * @var ?Motor
     */
    private ?Motor $motor = null;

    /**
     * Constructeur
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        
    }

    /**
     * Retourne la route courante
     * @return ?string
     */
    private function getCurrentRouteName() : ?string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        return $currentRequest?->attributes->get('_route');
    }

    /**
     * Modifie le moteur à gérer
     * @param Motor $motor
     * @return self
     */
    public function setMotor(Motor $motor) : self
    {
        $this->motor = $motor;
        return $this;
    }
    
    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-motors-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des moteurs
        $this->addListItem();

        // Ajout d'un moteur
        $this->addCreationItem();

        if($this->motor !== null)
        {
            $this->addMotorItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des moteurs
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.motors.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.motors.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_motors_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un moteur
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.motors.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.motors.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_motors_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un moteur
     * @return self
     */
    private function addMotorItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un moteur
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $motor = $this->motor;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.motors.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.motors.edit.alt_label', [
                'name' => $motor->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_motors_edit_index')
            ->setRouteParameters([
                'motorPublicId' => $motor->getPublicId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}