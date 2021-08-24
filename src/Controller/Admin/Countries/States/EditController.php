<?php

/**
 * Edition d'un état
 */

namespace App\Controller\Admin\Countries\States;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Controller\Admin\Countries\States\AbstractStateController;
use App\Entity\CountryState;
use App\Service\State\UploadStateFlagService;

final class EditController extends AbstractStateController {

    /**
     * Edition d'un pays
     * 
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param UploadStateFlagService $uploadService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/{countryStateCode}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'countryCode' => '[a-z]{2}',
                'countryStateCode' => '[a-z]{2}',
            ]
        ),
        ParamConverter('countryState', options: [ 'mapping' => [ 
            'countryCode' => 'country_code',
            'countryStateCode' => 'code',
        ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        CountryState $countryState,
        UploadStateFlagService $uploadService
    ) : Response
    {
        $this->setCountry($country);
        return new Response('@todo edit state');
    }

}