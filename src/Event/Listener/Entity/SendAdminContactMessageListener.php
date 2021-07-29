<?php

/**
 * Envoi du message de contact par email à l'administrateur du site
 */

namespace App\Event\Listener\Entity;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
/***/
use App\Entity\ContactMessage;
use App\Mail\ContactMessageMail;

final class SendAdminContactMessageListener {
    
    /**
     * Constructeur
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param MailerInterface $mailer
     * @param ContactMessageMail $contactMessageMail
     */
    public function __construct(
        private LoggerInterface $logger,
        private TranslatorInterface $translator,
        private MailerInterface $mailer, 
        private ContactMessageMail $contactMessageMail
    )
    {

    }

    /**
     * Envoi du message de contact par mail après l'enregistrement d'un message en base de données
     * @param ContactMessage $contactMessage
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postPersist(ContactMessage $contactMessage, LifecycleEventArgs $event) : void
    {
        $email = $this->contactMessageMail->getEmail($contactMessage);
        try {
            $this->mailer->send($email);
        } catch(TransportExceptionInterface) {
            $errorMessage = $this->translator->trans('contact.error', [
                'id' => $contactMessage->getId(),
            ], domain: 'mails');
            $this->logger->error($errorMessage);
        }
    }

}