<?php

/**
 * Liste des pays
 * On ne gére pas de pagination, il y a trop peu de nationalités diffèrentes
 */

namespace App\Controller\Admin\Countries;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\CountryRepository;

final class ListController extends AbstractCountryController {

    /**
     * Liste des pays
     * @param CountryRepository $countryRepository
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries',
            methods: [ 'GET', ]
        )
    ]
    public function index(CountryRepository $countryRepository) : Response
    {
        $countries = $countryRepository->findBy([], orderBy: [
            'code' => 'asc',
        ]);

        return $this->render('admin/countries/list.html.twig', [
            'countries' => $countries,
        ]);
    }
}