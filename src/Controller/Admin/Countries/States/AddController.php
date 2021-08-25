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
use App\Entity\{
    Country,
    CountryState
};
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Service\State\UploadStateFlagService;
use App\Form\Country\CountryStateType;

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
        
        $countryState = new CountryState();
        $countryState->setCountry($country);

        $form = $this->createForm(CountryStateType::class, $countryState);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            // Gestion de l'image
            $flagFile = $form->get('image')->getData();
            if($flagFile !== null)
            {
                $countryImage = $uploadService->attempt($flagFile, $countryState);
                $countryState->setImage($countryImage);
            }

            // Enregistrement
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($countryState);
                $entityManager->flush();
                $success = true;
            } catch(\Exception) {
                $success = false;
            }

            // Message Flash
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.countries.states.add.success' : 'admin.countries.states.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'stateName' => $countryState->getName(),
                'countryName' => $country->getName(),
            ]));

            // Redirection
            if($success)
            {
                return $this->redirectToRoute('app_admin_countries_states_list_index', [
                    'countryCode' => strtolower($country->getCode()),
                ]);
            }
        }

        return $this->renderForm('admin/countries/states/add.html.twig', [
            'form' => $form,
            'country' => $country,
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
            label: $this->translator->trans('admin.countries.states.add.label', domain: 'breadcrumb')
        ));
    }

}