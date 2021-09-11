<?php

/**
 * Liste des pays
 */

namespace App\Controller\Admin\Pilots;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\PilotRepository;
use App\UI\Pagination\Pagination;
use App\Form\PilotSearchingType;

final class ListController extends AbstractPilotController {

    /**
     * Liste des pilotes
     * @param Request $request
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
    public function index(
        Request $request, 
        PilotRepository $pilotRepository, 
        int $page = 1
    ) : Response
    {
        $itemsPerPage = 20;
        $pageNumber = max(1, $page);
        $offset = ($pageNumber - 1) * $itemsPerPage;

        // Gestion du formulaire de recherche
        $searching = null;
        $searchingForm = $this->createForm(PilotSearchingType::class);
        $searchingForm->handleRequest($request);
        if($searchingForm->isSubmitted() and $searchingForm->isValid())
        {
            $searching = $searchingForm->get('searching')->getData();
        }

        // Récupération des pilotes
        $pilots = $pilotRepository->findBySearching($searching, $itemsPerPage, $offset);
        $total = $pilotRepository->getTotalBySearching($searching);
        $pagination = new Pagination($itemsPerPage, $total);

        return $this->renderForm('admin/pilots/list.html.twig', [
            'searchingForm'  => $searchingForm,
            'pilots' => $pilots,
            'pagination' => $pagination,
            'searching' => $searching,
        ]);
    }
}