<?php

/**
 * Création d'un moteur
 */

namespace App\Controller\Admin\Motors;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Motor;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\MotorType;

final class AddController extends AbstractMotorController {

    /**
     * Ajout d'un moteur
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/motors/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator
    ) : Response
    {
        $motor = new Motor();

        $form = $this->createForm(MotorType::class, $motor);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($motor);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($motor->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.motors.add.success' : 'admin.motors.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $motor->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_motors_list_index');
            }

        }

        return $this->renderForm('admin/motors/add.html.twig', [
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
            label: $this->translator->trans('admin.motors.add.label', domain: 'breadcrumb')
        ));
    }

}