<?php

/**
 * Création d'une ville
 */

namespace App\Controller\Admin\Countries\States\Cities;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Entity\{
    Country,
    CountryState,
    City
};
use App\UI\Menus\Breadcrumb\BreadcrumbItem;

final class AddController extends AbstractCityController {

    /**
     * Ajout d'une ville
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param CountryState $countryState
     * @param UploadStateFlagService $uploadService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/{countryStateCode}/cities/add',
            methods: [ 'GET', 'POST', ],
            requirements: [ 
                'countryCode' => '[a-z]{2}', 
                'countryStateCode' => '[a-z]{2,3}',
            ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 'countryCode' => 'code' ] ]),
        ParamConverter('countryState', options: [ 'mapping' => [ 'countryStateCode' => 'code' ] ]),
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
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
        
        $city = new City();
        $city->setState($countryState);

        return $this->renderForm('layout/base.html.twig', [

        ]);
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.countries.states.cities.add.label', domain: 'breadcrumb')
        ));
    }

}