<?php

/**
 * Edition d'un pilote
 */

namespace App\Controller\Admin\Pilots;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Pilot;
use App\Form\Country\PilotType;

final class EditController extends AbstractPilotController {

    /**
     * Edition d'un pilote
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Pilot $pilot
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/pilots/{pilotPublicId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'pilotPublicId' => '[^\/]+',
            ]
        ),
        ParamConverter('pilot', options: [ 'mapping' => [ 'pilotPublicId' => 'public_id' ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Pilot $pilot,
    ) : Response
    {
        $originalPilot = clone $pilot;
        $this->setPilot($originalPilot);

        $form = $this->createForm(PilotType::class, $pilot);
        $form->handleRequest($request);

        return $this->renderForm('admin/pilots/edit.html.twig', [
            'form' => $form,
            'pilot' => $originalPilot,
        ]);
    }

}