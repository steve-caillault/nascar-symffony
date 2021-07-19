<?php

/**
 * Contrôleur d'index
 */

namespace App\Controller;

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
            path: '/'
        )
    ]
    public function index() : Response
    {
        return $this->render('site/default.html.twig');
    }
}