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