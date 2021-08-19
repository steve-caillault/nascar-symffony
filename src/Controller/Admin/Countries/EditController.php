<?php

/**
 * Edition d'un pays
 */

namespace App\Controller\Admin\Countries;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Entity\Country;

final class EditController extends AbstractCountryController {

    /**
     * Edition d'un pays
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'countryCode' => '[a-z]{2}',
            ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 'countryCode' => 'code' ] ])
    ]
    public function index(Country $country) : Response
    {
        return new Response('@todo countries/edit');
    }

}