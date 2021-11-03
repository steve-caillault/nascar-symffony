<?php

/**
 * Edition d'un moteur
 */

namespace App\Controller\Admin\Motors;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Motor;
use App\Form\MotorType;

final class EditController extends AbstractMotorController {

    /**
     * Edition d'un moteur
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Motor $motor
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/motors/{motorPublicId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'motorPublicId' => '[^\/]+',
            ]
        ),
        Entity('motor', expr: 'repository.findByPublicId(motorPublicId)')
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Motor $motor,
    ) : Response
    {
        // Si l'identifiant public est ancien, on redirige vers le plus récent
        $requestPublicId = $request->attributes->get('motorPublicId');
        $motorPublicId = $motor->getPublicId();
        if($requestPublicId !== $motorPublicId)
        {
            return $this->redirectToRoute('app_admin_motors_edit_index', [
                'motorPublicId' => $motorPublicId,
            ]);
        }

        $originalMotor = clone $motor;
        $this->setMotor($originalMotor);

        $form = $this->createForm(MotorType::class, $motor);
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
            $flashMessage = ($success) ? 'admin.motors.edit.success' : 'admin.motors.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $motor->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_motors_list_index');
            }
        }

        return $this->renderForm('admin/motors/edit.html.twig', [
            'form' => $form,
            'motor' => $originalMotor,
        ]);
    }

}