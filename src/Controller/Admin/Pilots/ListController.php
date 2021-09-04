<?php

/**
 * Liste des pays
 */

namespace App\Controller\Admin\Pilots;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\PilotRepository;
use App\UI\Pagination\Pagination;

final class ListController extends AbstractPilotController {

    /**
     * Liste des pilotes
     * @param PilotRepository $pilotRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/pilots/{page}',
            methods: [ 'GET', 'POST' ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1, ]
        )
    ]
    public function index(PilotRepository $pilotRepository, int $page = 1) : Response
    {
        $itemsPerPage = 20;
        $pageNumber = max(1, $page);
        $offset = ($pageNumber - 1) * $itemsPerPage;

        $searching = null;

        $pilots = $pilotRepository->findBySearching($searching, $pageNumber, $offset);

        $total = $pilotRepository->getTotalBySearching($searching);
        $pagination = new Pagination($itemsPerPage, $total);

        return $this->render('admin/pilots/list.html.twig', [
            'pilots' => $pilots,
            'pagination' => $pagination,
            'searching' => $searching,
        ]);
    }
}