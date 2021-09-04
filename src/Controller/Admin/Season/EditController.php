<?php

/**
 * Edition d'une saison
 */

namespace App\Controller\Admin\Season;

use Symfony\Component\HttpFoundation\{
    Request, 
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Controller\Admin\AbstractSeasonsController;
use App\Entity\Season;
use App\Form\SeasonType;

final class EditController extends AbstractSeasonsController {

    /**
     * Edition de la saison en paramètre
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/seasons/{seasonYear}/edit',
            methods: [ 'GET', 'POST' ]
        ),
        ParamConverter('season', options: [ 'mapping' => [ 'seasonYear' => 'year' ] ])
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        Season $season
    ) : Response
    {
        // On utilise clone pour ne pas modifier les titres et menus de la page en cas de problème de validation
        $originalSeason = clone $season;
        $this->setSeason($originalSeason);

        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            // Enregistrement
            try {
                $this->getDoctrine()->getManager()->flush();
                $success = true;
            } catch(\Throwable $e) {
                $success = false;
            }

            // Message Flash
            $flashMessage = ($success) ? 'admin.seasons.edit.success' : 'admin.seasons.edit.failure';
            $flashKey = ($success) ? 'success' : 'error';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'year' => $season->getYear(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_seasons_index');
            }
        }

        return $this->renderForm('admin/seasons/edit.html.twig', [
            'form' => $form,
            'season' => $originalSeason,
        ]);
    }

}