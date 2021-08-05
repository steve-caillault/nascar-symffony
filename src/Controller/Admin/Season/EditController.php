<?php

/**
 * Edition d'une saison
 */

namespace App\Controller\Admin\Season;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Controller\Admin\AbstractSeasonsController;
use App\Entity\Season;

final class EditController extends AbstractSeasonsController {

    /**
     * Edition de la saison en paramètre
     * @param Request $request
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/seasons/{seasonYear}/edit',
            methods: [ 'GET', 'POST' ]
        ),
        ParamConverter('season', options: [ 'mapping' => [ 'seasonYear' => 'year' ] ])
    ]
    public function index(Request $request, Season $season) : Response
    {
        return new Response(' @todo');
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
    }

}