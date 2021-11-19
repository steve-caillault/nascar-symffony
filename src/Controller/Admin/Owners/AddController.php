<?php

/**
 * Création d'un propriétaire
 */

namespace App\Controller\Admin\Owners;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
/***/
use App\Entity\Owner;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\OwnerType;

final class AddController extends AbstractOwnerController {

    /**
     * Ajout d'un propriétaire
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/owners/add',
            methods: [ 'GET', 'POST', ]
        )
    ]
    public function index(
        Request $request, 
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ) : Response
    {
        $owner = new Owner();

        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            try {
                $entityManager->persist($owner);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($owner->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'admin.owners.add.success' : 'admin.owners.add.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage, [
                'name' => $owner->getName(),
            ]));

            if($success)
            {
                return $this->redirectToRoute('app_admin_owners_list_index');
            }

        }

        return $this->renderForm('admin/owners/add.html.twig', [
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
            label: $this->translator->trans('admin.owners.add.label', domain: 'breadcrumb')
        ));
    }

}