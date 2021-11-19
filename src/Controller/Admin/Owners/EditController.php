<?php

/**
 * Edition d'un propriétaire
 */

namespace App\Controller\Admin\Owners;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
/***/
use App\Entity\Owner;
use App\Form\OwnerType;

final class EditController extends AbstractOwnerController {

    /**
     * Edition d'un propriétaire
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @param Owner $owner
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/owners/{ownerPublicId}/edit',
            methods: [ 'GET', 'POST', ],
            requirements: [
                'ownerPublicId' => '[^\/]+',
            ]
        ),
        Entity('owner', expr: 'repository.findByPublicId(ownerPublicId)')
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        Owner $owner,
    ) : Response
    {
        // Si l'identifiant public est ancien, on redirige vers le plus récent
        $requestPublicId = $request->attributes->get('ownerPublicId');
        $ownerPublicId = $owner->getPublicId();
        if($requestPublicId !== $ownerPublicId)
        {
            return $this->redirectToRoute('app_admin_owners_edit_index', [
                'ownerPublicId' => $ownerPublicId,
            ]);
        }

        $originalOwner = clone $owner;
        $this->setOwner($originalOwner);

        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager->flush();
                $success = true;
            } catch(\Throwable) {
                $success = false;
            }

            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.owners.edit.success' : 'admin.owners.edit.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $owner->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_owners_list_index');
            }
        }

        return $this->renderForm('admin/owners/edit.html.twig', [
            'form' => $form,
            'owner' => $originalOwner,
        ]);
    }

}