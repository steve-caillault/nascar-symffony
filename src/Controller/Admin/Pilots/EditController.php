<?php

/**
 * Edition d'un pilote
 */

namespace App\Controller\Admin\Pilots;

use Symfony\Component\HttpFoundation\{
    RedirectResponse,
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Pilot;
use App\Form\PilotType;

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
        Entity('pilot', expr: 'repository.findByPublicId(pilotPublicId)')
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Pilot $pilot,
    ) : Response
    {
        // Si l'identifiant public est ancien, on redirige vers le plus récent
        $requestPublicId = $request->attributes->get('pilotPublicId');
        $pilotPublicId = $pilot->getPublicId();
        if($requestPublicId !== $pilotPublicId)
        {
            return $this->redirectToRoute('app_admin_pilots_edit_index', [
                'pilotPublicId' => $pilotPublicId,
            ]);
        }

        $originalPilot = clone $pilot;
        $this->setPilot($originalPilot);

        $form = $this->createForm(PilotType::class, $pilot);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $success = true;
            } catch(\Throwable) {
                $success = false;
            }

            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.pilots.edit.success' : 'admin.pilots.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $pilot->getFullName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_pilots_list_index');
            }
        }

        return $this->renderForm('admin/pilots/edit.html.twig', [
            'form' => $form,
            'pilot' => $originalPilot,
        ]);
    }

}