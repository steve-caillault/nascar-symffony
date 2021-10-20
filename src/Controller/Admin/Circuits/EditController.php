<?php

/**
 * Edition d'un circuit
 */

namespace App\Controller\Admin\Circuits;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Circuit;
use App\Form\CircuitType;

final class EditController extends AbstractCircuitController {

    /**
     * Edition d'un circuit
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Circuit $circuit
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/circuits/{circuitId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'circuitId' => '[0-9]+',
            ]
        ),
        ParamConverter('circuit', options: [ 'mapping' => [ 'circuitId' => 'id' ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Circuit $circuit,
    ) : Response
    {
        $originalCircuit = clone $circuit;
        $this->setCircuit($originalCircuit);

        $form = $this->createForm(CircuitType::class, $circuit);
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
            $flashMessage = ($success) ? 'admin.circuits.edit.success' : 'admin.circuits.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $circuit->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_circuits_list_index');
            }
        }

        return $this->renderForm('admin/circuits/edit.html.twig', [
            'form' => $form,
            'circuit' => $originalCircuit,
        ]);
    }

}