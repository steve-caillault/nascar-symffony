<?php

/**
 * Contrôleur de contact
 */

namespace App\Controller\Site;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Breadcrumb\BreadcrumbItem;
use App\Form\ContactType as ContactForm;
use App\Entity\ContactMessage;

final class ContactController extends AbstractController
{

    /**
     * Page de contact
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/contact',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function index(Request $request, TranslatorInterface $translator) : Response
    {
        $session = $request->getSession();
        $sessionKeyNumberMessages = 'ContactMessage::COUNT_SENDING';
        $numberMessages = (int) $session->get($sessionKeyNumberMessages, 0);
        $limitMessages = 5;

        // Si l'utilisateur a envoyé trop de message, on ne cherche pas à gérer le formulaire
        $canSendMessage = ($numberMessages < $limitMessages);
        if(! $canSendMessage)
        {
            $errorMessage = $translator->trans('site.contact.too_much_sending');
            $this->addFlash('error', $errorMessage);
            return $this->render('site/contact.html.twig');
        }

        $contactMessage = new ContactMessage();
        $contactForm = $this->createForm(ContactForm::class, $contactMessage);
        $contactForm->handleRequest($request);

        // Traitement du formulaire
        if($contactForm->isSubmitted() and $contactForm->isValid())
        {
            
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->persist($contactMessage);
                $entityManager->flush();
            } catch(\Throwable) {
                
            }

            $success = ($contactMessage->getId() !== null);
            $flashKey = ($success) ? 'success' : 'error';
            $flashMessage = ($success) ? 'site.contact.success' : 'site.contact.failure';
            $this->addFlash($flashKey, $translator->trans($flashMessage));

            if($success)
            {
                $session = $request->getSession();
                $session->set($sessionKeyNumberMessages, $numberMessages + 1);

                return $this->redirectToRoute('app_site_default_index');
            }
        }

        return $this->renderForm('site/contact.html.twig', [
            'form' => $contactForm,
        ]);
    }

    /**
     * Alimente le fil d'Ariane
     * @return void
     */
    protected function fillBreadcrumb() : void
    {
        parent::fillBreadcrumb();
        $this->getBreadcrumb()->addItem(new BreadcrumbItem('site.contact.label'));
    }

}