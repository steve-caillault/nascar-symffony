<?php

/**
 * Menu de la section Pilotes
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\UI\Menus\Header\HeaderItemMenu;
use App\Entity\Pilot;

final class PilotsMenu extends HeaderMenu {

    /**
     * Pilote à gérer
     * @var ?Pilot
     */
    private ?Pilot $pilot = null;

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
     * Modifie le pilote à gérer
     * @param Pilot $pilot
     * @return self
     */
    public function setPilot(Pilot $pilot) : self
    {
        $this->pilot = $pilot;
        return $this;
    }
    
    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-pilots-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des pilotes
        $this->addListItem();

        // Ajout d'un pilote
        $this->addCreationItem();

        if($this->pilot !== null)
        {
            $this->addPilotItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des pilotes
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.pilots.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.pilots.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_pilots_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un pilote
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.pilots.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.pilots.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_pilots_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un pilote
     * @return self
     */
    private function addPilotItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un pilote
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $pilot = $this->pilot;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.pilots.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.pilots.edit.alt_label', [
                'name' => $pilot->getFullName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_pilots_edit_index')
            ->setRouteParameters([
                'pilotPublicId' => $pilot->getPublicId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}