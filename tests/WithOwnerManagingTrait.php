<?php

/**
 * Trait pour la gestion de propriétaire
 */

namespace App\Tests;

use App\Entity\{
    Owner,
    OwnerPublicIdHistory
};

trait WithOwnerManagingTrait {

    /**
     * Ajoute un identifiant public au propriétaire en paramètre
     * @param Owner $owner
     * @param string $publicId
     * @return void
     */
    private function addOwnerPublicId(Owner $owner, string $publicId) : void
    {
        $entityManager = $this->getEntityManager();
        $publicIdHistory = (new OwnerPublicIdHistory())
            ->setPublicId($publicId)
            ->setOwner($owner)
        ;
        $entityManager->persist($publicIdHistory);
        $entityManager->flush();
    }


}