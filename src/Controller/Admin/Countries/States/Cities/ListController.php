<?php

/**
 * Liste des ville d'un état
 * On ne gére pas de pagination, il y a trop peu de villes diffèrentes par état
 */

namespace App\Controller\Admin\Countries\States\Cities;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Repository\CityRepository;
use App\Entity\{
    City,
    Country,
    CountryState
};

final class ListController extends AbstractCityController {

    /**
     * Liste des villes d'un état
     * @param CityRepository $cityRepository
     * @param Country $country
     * @param CountryState $countryState
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/{countryStateCode}/cities',
            methods: [ 'GET', ],
            requirements: [ 
                'countryCode' => '[a-z]{2}',
                'countryStateCode' => '[a-z]{2,3}',
            ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 'countryCode' => 'code' ]]),
        ParamConverter('countryState', options: [ 'mapping' => [ 'countryStateCode' => 'code' ]])
    ]
    public function index(
        CityRepository $cityRepository, 
        Country $country,
        CountryState $countryState
    ) : Response
    {
        if($country !== $countryState->getCountry())
        {
            throw new NotFoundHttpException();
        }

        $this->setCountry($country);
        $this->setCountryState($countryState);

        $cities = $cityRepository->findBy([
            'state' => $countryState,
        ], orderBy: [
            'name' => 'asc',
        ]);

        return $this->render('admin/countries/states/cities/list.html.twig', [
            'cities' => $cities,
            'country' => $country,
            'countryState' => $countryState,
        ]);
    }
}