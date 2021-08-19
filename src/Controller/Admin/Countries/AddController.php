<?php

/**
 * Création d'un pays
 */

namespace App\Controller\Admin\Countries;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class AddController extends AbstractCountryController {

    /**
     * Ajout d'un pays
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index() : Response
    {
        return new Response('@todo countries/add');
    }

}