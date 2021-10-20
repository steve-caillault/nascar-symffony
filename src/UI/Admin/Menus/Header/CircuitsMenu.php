<?php

/**
 * Menu de la section Circuits
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\UI\Menus\Header\HeaderItemMenu;
use App\Entity\Circuit;

final class CircuitsMenu extends HeaderMenu {

    /**
     * Circuit à gérer
     * @var ?Circuit
     */
    private ?Circuit $circuit = null;

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
     * Modifie le circuit à gérer
     * @param Circuit $circuit
     * @return self
     */
    public function setCircuit(Circuit $circuit) : self
    {
        $this->circuit = $circuit;
        return $this;
    }
    
    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-circuits-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des circuits
        $this->addListItem();

        // Ajout d'un circuit
        $this->addCreationItem();

        if($this->circuit !== null)
        {
            $this->addCircuitItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des circuits
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.circuits.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.circuits.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_circuits_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un circuit
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.circuits.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.circuits.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_circuits_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un circuit
     * @return self
     */
    private function addCircuitItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un circuit
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $circuit = $this->circuit;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.circuits.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.circuits.edit.alt_label', [
                'name' => $circuit->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_circuits_edit_index')
            ->setRouteParameters([
                'circuitId' => $circuit->getId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}