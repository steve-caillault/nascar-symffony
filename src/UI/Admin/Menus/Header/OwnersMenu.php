<?php

/**
 * Menu de la section Propriétaires
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\UI\Menus\Header\HeaderItemMenu;
use App\Entity\Owner;

final class OwnersMenu extends HeaderMenu {

    /**
     * Propriétaire à gérer
     * @var ?Owner
     */
    private ?Owner $owner = null;

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
     * Modifie le propriétaire à gérer
     * @param Owner $owner
     * @return self
     */
    public function setOwner(Owner $owner) : self
    {
        $this->owner = $owner;
        return $this;
    }
    
    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-owners-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des propriétaires
        $this->addListItem();

        // Ajout d'un propriétaire
        $this->addCreationItem();

        if($this->owner !== null)
        {
            $this->addOwnerItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des propriétaires
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.owners.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.owners.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_owners_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un propriétaire
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.owners.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.owners.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_owners_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un propriétaire
     * @return self
     */
    private function addOwnerItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un propriétaire
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $owner = $this->owner;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.owners.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.owners.edit.alt_label', [
                'name' => $owner->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_owners_edit_index')
            ->setRouteParameters([
                'ownerPublicId' => $owner->getPublicId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}