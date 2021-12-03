<?php

/**
 * Liste des modèles de voiture
 */

namespace App\Controller\Admin\Cars;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\CarModelRepository;
use App\UI\Pagination\Pagination;

final class ListController extends AbstractCarController {

    /**
     * Liste des modèles de voiture
     * @param CarModelRepository $carModelRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/cars/{page}',
            methods: [ 'GET', ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1, ]
        )
    ]
    public function index(
        CarModelRepository $carModelRepository, 
        int $page = 1
    ) : Response
    {
        $itemsPerPage = 20;
        $pageNumber = max(1, $page);
        $offset = ($pageNumber - 1) * $itemsPerPage;

        // Récupération des modèles
        $carModels = $carModelRepository->getListWithMotorLoaded(orderBy: [
            'name' => 'asc',
        ], limit: $itemsPerPage, offset: $offset);
        $total = $carModelRepository->getTotal();
        $pagination = new Pagination($itemsPerPage, $total);

        return $this->renderForm('admin/cars/list.html.twig', [
            'cars' => $carModels,
            'pagination' => $pagination,
        ]);
    }
}