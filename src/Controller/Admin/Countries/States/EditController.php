<?php

/**
 * Edition d'un état
 */

namespace App\Controller\Admin\Countries\States;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\{
    CountryState,
    Country
};
use App\Service\State\UploadStateFlagService;
use App\Form\Country\CountryStateType;

final class EditController extends AbstractStateController {

    /**
     * Edition d'un pays
     * 
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param CountryState $countryState
     * @param UploadStateFlagService $uploadService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/{countryStateCode}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'countryCode' => '[a-z]{2}',
                'countryStateCode' => '[a-z]{2,3}',
            ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 
            'countryCode' => 'code',
        ] ]),
        ParamConverter('countryState', options: [ 'mapping' => [ 
            'countryStateCode' => 'code',
        ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Country $country,
        CountryState $countryState,
        UploadStateFlagService $uploadService
    ) : Response
    {
        if($country !== $countryState->getCountry())
        {
            throw new NotFoundHttpException();
        }

        $originalCountryState = clone $countryState;
        $this->setCountry($country);
        $this->setCountryState($originalCountryState);

        $form = $this->createForm(CountryStateType::class, $countryState);
        $form->handleRequest($request);

        $isValid = ($form->isSubmitted() and $form->isValid());
        $isInvalid = ($form->isSubmitted() and ! $form->isValid());

        $entityManager = $this->getDoctrine()->getManager();

        if($isValid)
        {
            // Gestion de l'image
            $flagFile = $form->get('image')->getData();
            if($flagFile !== null)
            {
                $countryImage = $uploadService->attempt($flagFile, $countryState);
                $countryState->setImage($countryImage);
            }

            try {
                $entityManager->flush();
                $success = true;
            } catch(\Exception) {
                $success = false;
            }

            // Message Flash
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.countries.states.edit.success' : 'admin.countries.states.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $countryState->getName(),
            ]));

            // Redirection
            if($success)
            {
                return $this->redirectToRoute('app_admin_countries_states_list_index', [
                    'countryCode' => strtolower($country->getCode()),
                ]);
            }
        }
        elseif($isInvalid)
        {
            $entityManager->clear(CountryState::class);
        }

        return $this->renderForm('admin/countries/states/edit.html.twig', [
            'form' => $form,
            'countryState' => $originalCountryState,
        ]);
    }

}