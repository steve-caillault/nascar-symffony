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

        $submitted = $form->isSubmitted();
        $isValid = ($submitted and $form->isValid());
        $isInvalid = ($submitted and ! $form->isValid());

        $entityManager = $this->getDoctrine()->getManager();

        if($isValid)
        {
            // Gestion de l'image
            $flagFile = $form->get('image')->getData();
            if($flagFile !== null)
            {
                $countryImage = $uploadService->attempt($flagFile, $country);
                $country->setImage($countryImage);
            }

            try {
                $entityManager->flush();
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
        elseif($isInvalid)
        {
            // @see https://github.com/steve-caillault/nascar-symfony/issues/2
            $entityManager->clear(Country::class);
        }

        return $this->renderForm('admin/countries/edit.html.twig', [
            'form' => $form,
            'country' => $originalCountry,
        ]);
    }

}