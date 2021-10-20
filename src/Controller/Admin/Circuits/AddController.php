<?php

/**
 * Création d'un circuit
 */

namespace App\Controller\Admin\Circuits;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Circuit;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\CircuitType;

final class AddController extends AbstractCircuitController {

    /**
     * Ajout d'un circuit
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/circuits/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator
    ) : Response
    {
        $circuit = new Circuit();

        $form = $this->createForm(Circuit::class, $circuit);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($circuit);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($circuit->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.circuits.add.success' : 'admin.circuits.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $circuit->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_circuits_list_index');
            }

        }

        return $this->renderForm('admin/circuits/add.html.twig', [
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
            label: $this->translator->trans('admin.circuits.add.label', domain: 'breadcrumb')
        ));
    }

}