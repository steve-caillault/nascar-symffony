<?php

/**
 * Contrôleur d'index
 */

namespace App\Controller\Site;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class DefaultController extends AbstractController
{

    /**
     * Page d'index
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/',
            methods: [ 'GET', ]
        )
    ]
    public function index() : Response
    {
        return $this->render('site/default.html.twig');
    }

}