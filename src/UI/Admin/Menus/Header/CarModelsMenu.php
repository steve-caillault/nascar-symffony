<?php

/**
 * Menu de la section Voitures
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\HeaderMenu;
use App\UI\Menus\Header\HeaderItemMenu;
use App\Entity\CarModel;

final class CarModelsMenu extends HeaderMenu {

    /**
     * Modèle de voiture à gérer
     * @var ?CarModel
     */
    private ?CarModel $carModel = null;

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
     * Modifie le modèle de voiture à gérer
     * @param CarModel $carModel
     * @return self
     */
    public function setCarModel(CarModel $carModel) : self
    {
        $this->carModel = $carModel;
        return $this;
    }
    
    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-car-models-menu';
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        // Liste des modèles
        $this->addListItem();

        // Ajout d'un modèle
        $this->addCreationItem();

        if($this->carModel !== null)
        {
            $this->addCarModelItem();
        }
        
    }

    /**
     * Ajoute l'élément de la liste des modèles
     * @return self
     */
    private function addListItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.cars.list.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.cars.list.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_cars_list_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute l'élément de l'ajout d'un modèle de voiture
     * @return self
     */
    private function addCreationItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.cars.add.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.cars.add.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_cars_add_index');

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

    /**
     * Ajoute les éléments de gestion d'un modèle de voiture
     * @return self
     */
    private function addCarModelItem() : self
    {
        $this->addEditItem();
        return $this;
    }

    /**
     * Ajout l'élément d'édition d'un modèle de voiture
     * @return self
     */
    private function addEditItem() : self
    {
        $currentRouteName = $this->getCurrentRouteName();
        $carModel = $this->carModel;

        $item = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.cars.edit.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.cars.edit.alt_label', [
                'name' => $carModel->getName(),
            ], domain: 'menus'))
            ->setRouteName('app_admin_cars_edit_index')
            ->setRouteParameters([
                'carModelId' => $carModel->getId(),
            ])
        ;

        if($currentRouteName === $item->getRouteName())
        {
            $item->addClass('selected');
        }

        return $this->addItem($item);
    }

}