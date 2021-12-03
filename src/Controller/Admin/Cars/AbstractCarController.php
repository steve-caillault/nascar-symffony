<?php

/**
 * Contrôleur de base pour la gestion des modèles de voiture
 */

namespace App\Controller\Admin\Cars;

use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\UI\Admin\Menus\Header\CarModelsMenu;
use App\Entity\CarModel;

abstract class AbstractCarController extends AdminAbstractController {

    /**
     * Menu des modèles de voiture
     * @var CarModelsMenu
     */
    private CarModelsMenu $car_models_menu;

    /**
     * Modèle de voiture géré
     * @var ?CarModel
     */
    private ?CarModel $car_model = null;

    /**
     * Initialise le menu des modèles de voiture
     * @param CarModelsMenu
     * @return void
     * @required
     */
    public function setCarModelsMenu(CarModelsMenu $carModelsMenu) : void
    {
        $this->car_models_menu = $carModelsMenu;
    }

    /**
     * Modifie le modèle de voiture à gérer
     * @param CarModel $carModel
     * @return self
     */
    protected function setCarModel(CarModel $carModel) : self
    {
        $this->car_model = $carModel;
        return $this;
    }

    /**
     * Retourne le modèle de voiture à gérer
     * @return ?CarModel
     */
    protected function getCarModel() : ?CarModel
    {
        return $this->car_model;
    }

    /**
     * Remplis l'en-tête avec les menus
     * @return void
     */
    protected function fillHeaderMenus() : void
    {
        parent::fillHeaderMenus();

        if($this->car_model !== null)
        {
            $this->car_models_menu->setCarModel($this->car_model);
        }

        $this->getHeaderMenus()->addAfter($this->car_models_menu, $this->getAdminMenu());
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
            label: $this->translator->trans('admin.cars.label', domain: 'breadcrumb'),
            altLabel: $this->translator->trans('admin.cars.alt_label', domain: 'breadcrumb'),
            routeName: 'app_admin_cars_list_index'
        ));

        $carModel = $this->car_model;
        if($carModel !== null)
        {
            $breadcrumb->addItem(new BreadcrumbItem(
                label: $carModel->getName(),
                altLabel: $this->translator->trans('admin.cars.edit.alt_label', [
                    'name' => $carModel->getName(),
                ], domain: 'breadcrumb'),
                routeName: 'app_admin_cars_edit_index',
                routeParameters: [
                    'carModelId' => $this->car_model->getId(),
                ],
            ));
        }
    }

}