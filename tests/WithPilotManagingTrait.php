<?php

/**
 * Trait pour la gestion de pilote
 */

namespace App\Tests;

use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};

trait WithPilotManagingTrait {

    /**
     * Ajoute un identifiant public au pilote en paramètre
     * @param Pilot $pilot
     * @param string $publicId
     * @return void
     */
    private function addPilotPublicId(Pilot $pilot, string $publicId) : void
    {
        $entityManager = $this->getEntityManager();
        $publicIdHistory = (new PilotPublicIdHistory())
            ->setPublicId($publicId)
            ->setPilot($pilot)
        ;
        $entityManager->persist($publicIdHistory);
        $entityManager->flush();
    }


}