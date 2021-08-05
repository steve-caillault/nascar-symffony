<?php

/**
 * Création d'une saison
 */

namespace App\Controller\Admin\Season;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Controller\Admin\AbstractSeasonsController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\SeasonType;
use App\Entity\Season;

final class AddController extends AbstractSeasonsController {

    /**
     * Ajout d'une saison
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/seasons/add',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index(Request $request, TranslatorInterface $translator) : Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($season);

            try {
                $entityManager->flush();
            } catch(\Throwable) {

            }

            $success = ($season->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.seasons.add.success' : 'admin.seasons.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'year' => $season->getYear(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_seasons_index');
            }
            
        }

        return $this->renderForm('admin/seasons/add.html.twig', [
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
            label: 'admin.seasons.add.label'
        ));
    }

}