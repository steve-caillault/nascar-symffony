<?php

/**
 * Liste des propriétaires
 */

namespace App\Controller\Admin\Owners;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\OwnerRepository;
use App\UI\Pagination\Pagination;

final class ListController extends AbstractOwnerController {

    /**
     * Liste des propriétaires
     * @param OwnerRepository $ownerRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/owner/{page}',
            methods: [ 'GET' ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1 ],
        )
    ]
    public function index(
        OwnerRepository $ownerRepository,
        int $page = 1
    ) : Response
    {
        $itemsPerPage = 20;
        $pageNumber = max(1, $page);
        $offset = ($pageNumber - 1) * $itemsPerPage;

        // @todo Gestion de la recherche ?

        // Récupération des propriétaires
        $owners = $ownerRepository->findBy(
            criteria: [], 
            orderBy: [ 'name' => 'asc', ], 
            limit: $itemsPerPage, 
            offset: $offset
        );

        $total = $ownerRepository->getTotal();
        $pagination = new Pagination($itemsPerPage, $total);

        return $this->renderForm('admin/owners/list.html.twig', [
            'owners' => $owners,
            'pagination' => $pagination,
        ]);
    }
}