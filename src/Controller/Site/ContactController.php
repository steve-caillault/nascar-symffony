<?php

/**
 * Contrôleur de contact
 */

namespace App\Controller\Site;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\UI\Menus\Breadcrumb\BreadcrumbItem;

final class ContactController extends AbstractController
{

    /**
     * Page de contact
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/contact',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index() : Response
    {
        return $this->render('site/contact.html.twig');
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem('site.contact.label'));
    }

}