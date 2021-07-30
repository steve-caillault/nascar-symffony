<?php

/**
 * Création d'une saison
 */

namespace App\Controller\Admin\Season;

use App\Controller\Admin\AbstractSeasonsController;
use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\SeasonType;
use App\Entity\Season;

final class AddController extends AbstractSeasonsController {

    /**
     * Ajout d'une saison
     * @param Request $request
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/seasons/add',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index(Request $request) : Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {

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