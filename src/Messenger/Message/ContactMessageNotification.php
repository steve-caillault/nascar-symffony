<?php

/**
 * Message pour Messenger du mail de contact
 */

namespace App\Messenger\Message;

use App\Entity\ContactMessage;

final class ContactMessageNotification {

    /**
     * Constructeur
     * @param ContactMessage $contactMessage
     */
    public function __construct(private ContactMessage $contactMessage)
    {

    }

    /**
     * Retourne le message de contact
     * @return ContactMessage
     */
    public function getContactMessage() : ContactMessage
    {
        return $this->contactMessage;
    }


}