<?php

/**
 * Edition d'une ville
 */

namespace App\Controller\Admin\Countries\States\Cities;

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
    Country,
    City
};
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\Country\CityType;

final class EditController extends AbstractCityController {

    /**
     * Edition d'une ville
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Country $country
     * @param CountryState $countryState
     * @param City $city
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/countries/{countryCode}/states/{countryStateCode}/cities/{cityId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'countryCode' => '[a-z]{2}',
                'countryStateCode' => '[a-z]{2,3}',
                'cityId' => '[0-9]+',
            ]
        ),
        ParamConverter('country', options: [ 'mapping' => [ 
            'countryCode' => 'code',
        ] ]),
        ParamConverter('countryState', options: [ 'mapping' => [ 
            'countryStateCode' => 'code',
        ] ]),
        ParamConverter('city', options: [ 'mapping' => [ 
            'cityId' => 'id',
        ] ]),
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Country $country,
        CountryState $countryState,
        City $city
    ) : Response
    {
        if($country !== $countryState->getCountry() or $countryState !== $city->getState())
        {
            throw new NotFoundHttpException();
        }

        $originalCity = clone $city;

        $this->setCountry($country);
        $this->setCountryState($countryState);
        $this->setCity($originalCity);

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        $isValid = ($form->isSubmitted() and $form->isValid());
        $isInvalid = ($form->isSubmitted() and ! $form->isValid());

        $entityManager = $this->getDoctrine()->getManager();

        if($isValid)
        {
            try {
                $entityManager->flush();
                $success = true;
            } catch(\Exception) {
                $success = false;
            }

            // Message Flash
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.countries.states.cities.edit.success' : 'admin.countries.states.cities.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $city->getName(),
            ]));

            // Redirection
            if($success)
            {
                return $this->redirectToRoute('app_admin_countries_states_cities_list_index', [
                    'countryCode' => strtolower($country->getCode()),
                    'countryStateCode' => strtolower($countryState->getCode()),
                ]);
            }
        }
        elseif($isInvalid)
        {
            $entityManager->clear(City::class);
        }

        return $this->renderForm('admin/countries/states/cities/edit.html.twig', [
            'form' => $form,
            'city' => $originalCity,
        ]);
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        
        // Ajout de l'élément vers l'édition de la ville
        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            label: $this->getCity()->getName(),
        ));
    }

}