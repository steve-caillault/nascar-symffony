<?php

/**
 * Test du repository des propriétaires
 */

namespace App\Tests\Repositories;

use App\DataFixtures\OwnerFixtures;
use App\Tests\{
    BaseTestCase,
    WithOwnerManagingTrait
};
use App\Entity\Owner;

final class OwnerRepositoryTest extends BaseTestCase {
    
    use PublicIdRepositoryTrait, WithOwnerManagingTrait;

    /**
     * Test de récupération d'un propriétaire par son identifiant
     * @param string $publicId
     * @param bool $mustFound Vrai si la recherche par identifiant public doit fonctionner
     * @dataProvider publidIdsProvider
     * @return void
     */
    public function testRetrieveByPublicId(string $publicId, bool $mustFound) : void
    {
        $this->executeFixtures([ OwnerFixtures::class, ]);

        $ownerIdToFind = 3; // Hendrick Motorsports

        $ownerRepository = $this->getRepository(Owner::class);

        // Création d'un ancien identifiant public
        $hendrickOwner = $ownerRepository->find($ownerIdToFind);
        $this->addOwnerPublicId($hendrickOwner, 'hendrick-motorsports-2022');

        // Vérification du propriétaire
        $ownerIdExpected = ($mustFound) ? $ownerIdToFind : null;

        $owner = $ownerRepository->findByPublicId($publicId);
        $this->assertEquals($ownerIdExpected, $owner?->getId());
    }

    /**
     * Provider pour la recherche de propriétaire par identifiant public
     * @return array
     */
    public function publidIdsProvider() : array
    {
        return [
            'without-history' => [
                'hendrick-motorsports', true,
            ],
            'with-history' => [
                'hendrick-motorsports-2022', true,
            ],
            'not-found' => [
                'hendrick', false,
            ],
        ];
    }
}