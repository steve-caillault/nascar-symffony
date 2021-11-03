<?php

/**
 * Liste des moteurs
 */

namespace App\Controller\Admin\Motors;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Repository\MotorRepository;

final class ListController extends AbstractMotorController {

    /**
     * Liste des moteur
     * @param MotorRepository $motorRepository
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/motors',
            methods: [ 'GET' ],
        )
    ]
    public function index(MotorRepository $motorRepository) : Response
    {
        // Récupération des moteurs
        $motors = $motorRepository->findBy([], [ 'name' => 'asc', ]);

        return $this->renderForm('admin/motors/list.html.twig', [
            'motors' => $motors,
        ]);
    }
}