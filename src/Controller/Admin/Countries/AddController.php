<?php

/**
 * Création d'un pays
 */

namespace App\Controller\Admin\Countries;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Country;
use App\Form\CountryType;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Service\Country\UploadCountryFlagService;

final class AddController extends AbstractCountryController {

    /**
     * Ajout d'un pays
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UploadCountryFlagService $uploadService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        UploadCountryFlagService $uploadService
    ) : Response
    {
        $country = new Country();

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

            // Enregistrement
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($country);
                $entityManager->flush();
                $success = true;
            } catch(\Exception) {
                $success = false;
            }

            // Message Flash
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.countries.add.success' : 'admin.countries.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $country->getName(),
            ]));

            // Redirection
            if($success)
            {
                return $this->redirectToRoute('app_admin_countries_list_index');
            }
        }

        return $this->renderForm('admin/countries/add.html.twig', [
            'form' => $form,
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
            label: $this->translator->trans('admin.countries.add.label', domain: 'breadcrumb')
        ));
    }

}