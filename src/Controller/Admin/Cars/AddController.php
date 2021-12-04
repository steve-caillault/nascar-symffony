<?php

/**
 * Création d'un modèle de voiture
 */

namespace App\Controller\Admin\Cars;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\CarModel;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\CarModelType;

final class AddController extends AbstractCarController {

    /**
     * Ajout d'un modèle de voiture
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/cars/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator
    ) : Response
    {
        $car = new CarModel();

        $form = $this->createForm(CarModelType::class, $car);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($car);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($car->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.cars.add.success' : 'admin.cars.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $car->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_cars_list_index');
            }

        }

        return $this->renderForm('admin/cars/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.cars.add.label', domain: 'breadcrumb')
        ));
    }

}