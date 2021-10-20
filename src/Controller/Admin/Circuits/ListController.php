<?php

/**
 * Liste des circuits
 */

namespace App\Controller\Admin\Circuits;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\CircuitRepository;
use App\UI\Pagination\Pagination;
use App\Form\CircuitSearchingType;

final class ListController extends AbstractCircuitController {

    /**
     * Liste des circuit
     * @param Request $request
     * @param CircuitRepository $circuitRepository
     * @param int $page
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/circuits/{page}',
            methods: [ 'GET', 'POST' ],
            requirements: [ 'page' => '[0-9]+' ],
            defaults: [ 'page' => 1, ]
        )
    ]
    public function index(
        Request $request, 
        CircuitRepository $circuitRepository, 
        int $page = 1
    ) : Response
    {
        $itemsPerPage = 20;
        $pageNumber = max(1, $page);
        $offset = ($pageNumber - 1) * $itemsPerPage;

        // Gestion du formulaire de recherche
        $searching = null;
        $searchingForm = $this->createForm(CircuitSearchingType::class);
        $searchingForm->handleRequest($request);
        if($searchingForm->isSubmitted() and $searchingForm->isValid())
        {
            $searching = $searchingForm->get('searching')->getData();
        }

        // Récupération des circuits
        $circuits = $circuitRepository->findBySearching($searching, $itemsPerPage, $offset);
        $total = $circuitRepository->getTotalBySearching($searching);
        $pagination = new Pagination($itemsPerPage, $total);

        return $this->renderForm('admin/circuits/list.html.twig', [
            'searchingForm'  => $searchingForm,
            'circuits' => $circuits,
            'pagination' => $pagination,
            'searching' => $searching,
        ]);
    }
}