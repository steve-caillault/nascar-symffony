<?php

/**
 * Edition d'un modèle de voiture
 */

namespace App\Controller\Admin\Cars;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\CarModel;
use App\Form\CarModelType;

final class EditController extends AbstractCarController {

    /**
     * Edition d'un modèle de voiture
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param CarModel $car
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/cars/{carModelId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'carModelId' => '[0-9]+',
            ]
        ),
        ParamConverter('car', options: [ 'mapping' => [ 'carModelId' => 'id' ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        CarModel $car,
    ) : Response
    {
        $originalCar = clone $car;
        $this->setCarModel($originalCar);

        $form = $this->createForm(CarModelType::class, $car);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $success = true;
            } catch(\Throwable) {
                $success = false;
            }

            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.cars.edit.success' : 'admin.cars.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $car->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_cars_list_index');
            }
        }

        return $this->renderForm('admin/cars/edit.html.twig', [
            'form' => $form,
            'car' => $originalCar,
        ]);
    }

}