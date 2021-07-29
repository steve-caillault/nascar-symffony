<?php

/**
 * Handler pour Messenger de l'envoi du mail d'un message de contact
 */

namespace App\Messenger\Handler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
/***/
use App\Messenger\Message\ContactMessageNotification;
use App\Mail\ContactMessageMail;

final class ContactMessageNotificationHandler implements MessageHandlerInterface
{
    /**
     * Constucteur
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     * @param ContactMessageMail $contactMessageMail
     */
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer, 
        private ContactMessageMail $contactMessageMail
    )
    {
        
    }

    /**
     * Envoi du mail 
     * @param ContactMessageNotification $notification
     */
    public function __invoke(ContactMessageNotification $notification)
    {
        $contactMessage = $notification->getContactMessage();
        $mail = $this->contactMessageMail->getEmail($contactMessage);

        try {
            $this->mailer->send($mail);
        } catch(TransportExceptionInterface) {
            $errorMessage = $this->translator->trans('contact.error', [
                'id' => $contactMessage->getId(),
            ], domain: 'mails');
            $this->logger->error($errorMessage);
        }

    }
}