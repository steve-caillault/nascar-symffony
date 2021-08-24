<?php

/**
 * Création d'un état à un pays
 */

namespace App\Controller\Admin\Countries\States;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/***/
use App\Entity\Country;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Service\State\UploadStateFlagService;

final class AddController extends AbstractStateController {

    /**
     * Ajout d'un état à un pays
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param UploadStateFlagService $uploadService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/add',
            methods: [ 'GET', 'POST', ],
            requirements: [ 'countryCode' => '[a-z]{2}', ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 'countryCode' => 'code' ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Country $country,
        UploadStateFlagService $uploadService
    ) : Response
    {
        $this->setCountry($country);
        return new Response('@todo state add');
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            label: $this->translator->trans('admin.countries.states.add.label', domain: 'breadcrumb')
        ));
    }

}