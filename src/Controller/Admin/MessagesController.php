<?php

/**
 * Gestion des messages reçus
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\ContactMessageRepository;
use App\UI\Pagination\Pagination;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;

final class MessagesController extends AdminAbstractController {

    /**
     * Liste des messages
     * @param ContactMessageRepository $messageRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/messages/{page}',
            methods: [ 'GET', ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1 ]
        )
    ]
    public function index(ContactMessageRepository $messageRepository, int $page) : Response
    {
        $itemsPerPage = 20;
        $offset = (max(1, $page) - 1) * $itemsPerPage;

        $messages = $messageRepository->findBy([], orderBy: [
            'createdAt' => 'desc',
        ], limit: $itemsPerPage, offset: $offset);

        $total = $messageRepository->getTotal();

        $pagination = new Pagination($itemsPerPage, $total);


        return $this->render('admin/messages/list.html.twig', [
            'messages' => $messages,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem('admin.contact_messages.label'));
    }

}