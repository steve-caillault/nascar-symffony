<?php

/**
 * Liste des saisons
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\SeasonRepository;
use App\UI\Pagination\Pagination;

final class SeasonsController extends AbstractSeasonsController {

    /**
     * Liste des saisons
     * @param SeasonRepository $seasonRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/seasons/{page}',
            methods: [ 'GET', ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1 ]
        )
    ]
    public function index(SeasonRepository $seasonRepository, int $page) : Response
    {
        $itemsPerPage = 20;
        $offset = (max(1, $page) - 1) * $itemsPerPage;

        $seasons = $seasonRepository->findBy([], orderBy: [
            'year' => 'desc',
        ], limit: $itemsPerPage, offset: $offset);

        $total = $seasonRepository->getTotal();

        $pagination = new Pagination($itemsPerPage, $total);

        return $this->render('admin/seasons/list.html.twig', [
            'seasons' => $seasons,
            'pagination' => $pagination,
        ]);
    }
}