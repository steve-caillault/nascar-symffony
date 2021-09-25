<?php

/**
 * Création d'un pilote
 */

namespace App\Controller\Admin\Pilots;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Pilot;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\PilotType;

final class AddController extends AbstractPilotController {

    /**
     * Ajout d'un pilote
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/pilots/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator
    ) : Response
    {
        $pilot = new Pilot();

        $form = $this->createForm(PilotType::class, $pilot);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($pilot);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($pilot->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.pilots.add.success' : 'admin.pilots.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $pilot->getFullName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_pilots_list_index');
            }

        }

        return $this->renderForm('admin/pilots/add.html.twig', [
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
            label: $this->translator->trans('admin.pilots.add.label', domain: 'breadcrumb')
        ));
    }

}