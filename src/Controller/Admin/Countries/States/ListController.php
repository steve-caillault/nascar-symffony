<?php

/**
 * Liste des pays
 * On ne gére pas de pagination, il y a trop peu de nationalités diffèrentes
 */

namespace App\Controller\Admin\Countries\States;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Repository\CountryStateRepository;
use App\Entity\Country;
use App\UI\Pagination\Pagination;

final class ListController extends AbstractStateController {

    /**
     * Liste des états d'un pays
     * @param CountryStateRepository $countryStateRepository
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/{page}',
            methods: [ 'GET', ],
            requirements: [ 
                'countryCode' => '[a-z]{2}',
                'page' => '[0-9]+',
            ],
            defaults: [ 'page' => 1 ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 'countryCode' => 'code' ]])
    ]
    public function index(
        CountryStateRepository $countryStateRepository, 
        Country $country, 
        int $page = 1
    ) : Response
    {
        $this->setCountry($country);

        $itemsPerPage = 20;
        $offset = ($page - 1) * $itemsPerPage;

        $countriesStates = $countryStateRepository->findBy([
            'country' => $country,
        ], orderBy: [
            'code' => 'asc',
        ], offset: $offset);

        $total = $countryStateRepository->getTotalFrom($country);
        $pagination = new Pagination($itemsPerPage, );

        return $this->render('admin/countries/states/list.html.twig', [
            'countriesStates' => $countriesStates,
            'country' => $country,
            'pagination' => $pagination,
        ]);
    }
}