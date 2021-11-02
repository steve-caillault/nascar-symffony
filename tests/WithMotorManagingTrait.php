<?php

/**
 * Trait pour la gestion de moteur
 */

namespace App\Tests;

use App\Entity\{
    Motor,
    MotorPublicIdHistory
};

trait WithMotorManagingTrait {

    /**
     * Ajoute un identifiant public au moteur en paramètre
     * @param Motor $motor
     * @param string $publicId
     * @return void
     */
    private function addMotorPublicId(Motor $motor, string $publicId) : void
    {
        $entityManager = $this->getEntityManager();
        $publicIdHistory = (new MotorPublicIdHistory())
            ->setPublicId($publicId)
            ->setMotor($motor)
        ;
        $entityManager->persist($publicIdHistory);
        $entityManager->flush();
    }


}