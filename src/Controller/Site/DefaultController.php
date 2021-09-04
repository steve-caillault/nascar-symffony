<?php

/**
 * Contrôleur d'index
 */

namespace App\Controller\Site;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\PilotRepository;
use App\Entity\{
    Pilot, PilotPublicId
};

final class DefaultController extends AbstractController
{

    /**
     * Page d'index
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/',
            methods: [ 'GET', ]
        )
    ]
    public function index(PilotRepository $pilotRepository) : Response
    {
        //$pilot = $pilotRepository->find(1);
        //$pilot->setLastName('NewName' . time())->setPublicId(time());
        

        /*$city = $cityRepository->find(1);
        $pilot = (new Pilot())
            ->setFirstName('FirstName')
            ->setLastName('LastName')
            ->setPublicId('current-id')
            ->setBirthCity($city)
            ->setBirthDate(new \DateTimeImmutable('1998-07-25'))
        ;
*/
       // $entityManager = $this->getDoctrine()->getManager();
       // $entityManager->persist($pilot);
       // $entityManager->flush();

        return $this->render('site/default.html.twig');
    }

}