<?php

/**
 * Page d'accueil du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;

final class DefaultController extends AdminAbstractController {

    /**
     * Index du panneau d'administration
     * @return Response
     */
    #[
        RouteAnnotation(
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index() : Response
    {
        return $this->render('admin/default.html.twig');
    }

}