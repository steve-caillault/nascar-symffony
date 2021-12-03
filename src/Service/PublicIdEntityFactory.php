<?php

/**
 * Factory pour les entités gérant un identifiant public
 */

namespace App\Service;

use App\Entity\{
    PublicIdEntityInterface,
    PublicIdHistoryEntityInterface,
    PilotPublicIdHistory,
    MotorPublicIdHistory,
    OwnerPublicIdHistory,
    Pilot,
    Motor,
    Owner
};

final class PublicIdEntityFactory {

    /**
     * Retourne un objet historique initialisé pour l'entité en paramètre
     * @param PublicIdEntityInterface $entity
     * @return PublicIdHistoryEntityInterface
     */
    public function get(PublicIdEntityInterface $entity) : PublicIdHistoryEntityInterface
    {
        $entityHistory = match(get_class($entity)) {

            Pilot::class => (new PilotPublicIdHistory())->setPilot($entity),
            Motor::class => (new MotorPublicIdHistory())->setMotor($entity),
            Owner::class => (new OwnerPublicIdHistory())->setOwner($entity),
            default => null
        };

        return $entityHistory;
    }

}