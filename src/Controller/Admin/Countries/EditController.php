<?php

/**
 * Edition d'un pays
 */

namespace App\Controller\Admin\Countries;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Country;
use App\Form\Country\CountryType;
use App\Service\State\UploadStateFlagService;

final class EditController extends AbstractCountryController {

    /**
     * Edition d'un pays
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param UploadStateFlagService $uploadService
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
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Country $country,
        UploadStateFlagService $uploadService
    ) : Response
    {
        $originalCountry = clone $country;
        $this->setCountry($originalCountry);

        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            // Gestion de l'image
            $flagFile = $form->get('image')->getData();
            if($flagFile !== null)
            {
                $countryImage = $uploadService->attempt($flagFile, $country);
                $country->setImage($countryImage);
            }

            try {
                $this->getDoctrine()->getManager()->flush();
                $success = true;
            } catch(\Exception) {
                $success = false;
            }

            // Message Flash
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.countries.edit.success' : 'admin.countries.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $country->getName(),
            ]));

            // Redirection
            if($success)
            {
                return $this->redirectToRoute('app_admin_countries_list_index');
            }
        }

        return $this->renderForm('admin/countries/edit.html.twig', [
            'form' => $form,
            'country' => $originalCountry,
        ]);
    }

}