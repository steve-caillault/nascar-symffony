<?php

/**
 * Test du repository des moteurs
 */

namespace App\Tests\Repositories;

use App\DataFixtures\MotorFixtures;
use App\Tests\{
    BaseTestCase,
    WithMotorManagingTrait
};
use App\Entity\Motor;

final class MotorRepositoryTest extends BaseTestCase {
    
    use PublicIdRepositoryTrait, WithMotorManagingTrait;

    /**
     * Test de récupération d'un moteur par son identifiant
     * @param string $publicId
     * @param bool $mustFound Vrai si la recherche par identifiant public doit fonctionner
     * @dataProvider publidIdsProvider
     * @return void
     */
    public function testRetrieveByPublicId(string $publicId, bool $mustFound) : void
    {
        $this->executeFixtures([ MotorFixtures::class, ]);

        $motorIdToFind = 1; // Chevrolet

        $motorRepository = $this->getRepository(Motor::class);

        // Création d'un ancien identifiant public
        $chevroletMotor = $motorRepository->find($motorIdToFind);
        $this->addMotorPublicId($chevroletMotor, 'chevrolet-2022');

        // Vérification du moteur
        $motorIdExpected = ($mustFound) ? $motorIdToFind : null;

        $motor = $motorRepository->findByPublicId($publicId);
        $this->assertEquals($motorIdExpected, $motor?->getId());
    }

    /**
     * Provider pour la recherche de moteur par identifiant public
     * @return array
     */
    public function publidIdsProvider() : array
    {
        return [
            'without-history' => [
                'chevrolet', true,
            ],
            'with-history' => [
                'chevrolet-2022', true,
            ],
            'not-found' => [
                'dodge', false,
            ],
        ];
    }
}