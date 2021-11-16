<?php

/**
 * Contrôleur de base pour la gestion des propriétaires
 */

namespace App\Controller\Admin\Owners;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\OwnersMenu;
use App\Entity\Owner;

abstract class AbstractOwnerController extends AdminAbstractController {

    /**
     * Menu des propriétaires
     * @var OwnersMenu
     */
    private OwnersMenu $owners_menu;

    /**
     * Propriétaire géré
     * @var ?Owner
     */
    private ?Owner $owner = null;

    /**
     * Initialise le menu des propriétaires
     * @param OwnersMenu
     * @return void
     * @required
     */
    public function setOwnersMenu(OwnersMenu $ownersMenu) : void
    {
        $this->owners_menu = $ownersMenu;
    }

    /**
     * Modifie le propriétaire à gérer
     * @param Owner $owner
     * @return self
     */
    protected function setOwner(Owner $owner) : self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Retourne le propriétaire à gérer
     * @return ?Owner
     */
    protected function getOwner() : ?Owner
    {
        return $this->owner;
    }

    /**
     * Remplit l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->owner !== null)
        {
            $this->owners_menu->setOwner($this->owner);
        }

        $this->getHeaderMenus()->addAfter($this->owners_menu, $this->getAdminMenu());
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
            label: $this->translator->trans('admin.owners.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.owners.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_owners_list_index'
        ));

        $owner = $this->owner;
        if($owner !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $owner->getName(),
                altLabel: $this->translator->trans('admin.owners.edit.alt_label', [
                    'name' => $owner->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_owners_edit_index',
                routeParameters: [
                    'ownerPublicId' => strtolower($this->owner->getPublicId()),
                ],
            ));
        }
    }

}