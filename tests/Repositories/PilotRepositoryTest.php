<?php

/**
 * Test du repository des pilotes
 */

namespace App\Tests\Repositories;

use App\DataFixtures\PilotFixtures;
use App\Tests\{
    BaseTestCase,
    WithPilotManagingTrait
};
use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};

final class PilotRepositoryTest extends BaseTestCase {
    
    use WithPilotManagingTrait;

    /**
     * Test de récupération d'un pilot par son identifiant
     * @param string $publicId
     * @param bool $mustFound Vrai si la recherche par identifiant public doit fonctionner
     * @dataProvider publidIdsProvider
     * @return void
     */
    public function testRetrieveByPublicId(string $publicId, bool $mustFound) : void
    {
        $this->executeFixtures([ PilotFixtures::class, ]);

        $pilotIdToFind = 17; // Denny Hamlin

        $pilotRepository = $this->getRepository(Pilot::class);

        // Création d'un ancien identifiant public
        $dennyHamlinPilot = $pilotRepository->find($pilotIdToFind);
        $this->addPilotPublicId($dennyHamlinPilot, 'denny-hamlin-2');

        // Vérification du pilote
        $pilotIdExpected = ($mustFound) ? $pilotIdToFind : null;
        $pilot = $pilotRepository->findByPublicId($publicId);
        $this->assertEquals($pilotIdExpected, $pilot?->getId());
    }

    /**
     * Provider pour la recherche de pilote par identifiant public
     * @return array
     */
    public function publidIdsProvider() : array
    {
        return [
            'without-history' => [
                'denny-hamlin', true,
            ],
            'with-history' => [
                'denny-hamlin-2', true,
            ],
            'not-found' => [
                'richard-lee-petty', false,
            ]
        ];
    }

    /**
     * Test sans pilote
     * @return void
     */
    public function testWithoutPilot() : void
    {
        $pilotRepository =  $this->getRepository(Pilot::class);
        $list = $pilotRepository->findBySearching(null, 10, 0);
        $count = $pilotRepository->getTotalBySearching(null);

        $this->assertEquals(0, $count);
        $this->assertEquals([], $list);
    }

    /**
     * Test avec des pilotes
     * @param ?string $searching Terme de la recherche
     * @param int $limit
     * @param int $offset
     * @dataProvider withPilotsProvider
     * @return void
     */
    public function testWithPilots(?string $searching, int $limit, int $offset) : void
    {
        $this->executeFixtures([ PilotFixtures::class ]);

        // Données des pilotes qu'on devrait obtenir
        $pilotsData = $this->getPilotListExpected($searching);
        $expectedNumber = count($pilotsData);
        $pilotsDataExpected = array_slice($pilotsData, $offset, $limit);

        // Récupération des pilotes à partir du repository
        $pilotRepository =  $this->getRepository(Pilot::class);
        $list = $pilotRepository->findBySearching($searching, $limit, $offset);
        $count = $pilotRepository->getTotalBySearching($searching);

        // Formatage des données de l'entité pour la vérification
        $listData = [];
        array_walk($list, function($pilot) use(&$listData) {
            $listData[] = [
                'id' => $pilot->getId(),
                'publicId' => $pilot->getPublicId(),
                'firstName' => $pilot->getFirstName(),
                'lastName' => $pilot->getLastName(),
                'fullName' => $pilot->getFullName(),
                'birthDate' => $pilot->getBirthDate()->format('Y-m-d'),
                'birthCity' => $pilot->getBirthCity()->getName(),
                'birthState' => $pilot->getBirthCity()->getState()->getCode(),
            ];
        });

        $this->assertEquals($expectedNumber, $count);
        $this->assertEquals($pilotsDataExpected, $listData);
    }

    /**
     * Provider pour les tests sur la liste des pilotes
     * @return array
     */
    public function withPilotsProvider() : array
    {
        return array(
            [
                // Pas de recherche, limit 10, offset 0
                null, 10, 0,
            ],
            [
                // Pas de recherche, limit 15, offset 20
                null, 15, 20,
            ],
            [
                // Recherche partielle par prénom, limit 8, offset 2
                'Kyle', 8, 2,
            ],
            [
                // Recherche partielle par nom, limit 20, offset 0
                'Busch', 20, 0,
            ],
            [
                // Recherche complète
                'Kyle Larson', 20, 0,
            ],
            [
                // Recherche complète sans résultat sur l'offset
                'Denny Hamlin', 1, 1,
            ]
        );
    }

    /**
     * Retourne la liste des pilotes qu'on devrait obtenir après la recherche et le tri
     * @param ?string $searching Terme de la recherche
     * @return array
     */
    private function getPilotListExpected(?string $searching) : array
    {
        $pilotFixtures = $this->getService(PilotFixtures::class);
        $data = $pilotFixtures->getDataFromCSV();

        // Tri les pilotes par nom complet croissant
        usort($data, function($pilot1, $pilot2) {
            $fullName1 = $pilot1['fullName'];
            $fullName2 = $pilot2['fullName'];
            return ($fullName1 < $fullName2 ? -1 : 1);
        });

        // Filtre les pilotes correspondant à la recherche
        if($searching !== null)
        {
            $data = array_filter($data, fn($item) => str_contains($item['fullName'], $searching));
        }

        return $data;
    }
}