<?php

/**
 * Envoi du message de contact par email à l'administrateur du site
 */

namespace App\Event\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;
/***/
use App\Entity\ContactMessage;
use App\Messenger\Message\ContactMessageNotification;

final class SendAdminContactMessageListener {
    
    /**
     * Constructeur
     * @param MessageBusInterface $messageBus
     */
    public function __construct(private MessageBusInterface $messageBus,)
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
        $notification = new ContactMessageNotification($contactMessage);
        $this->messageBus->dispatch($notification);
    }

}