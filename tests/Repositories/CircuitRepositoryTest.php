<?php

/**
 * Test du repository des circuits
 */

namespace App\Tests\Repositories;

use App\DataFixtures\CircuitFixtures;
use App\Tests\BaseTestCase;
use App\Entity\Circuit;

final class CircuitRepositoryTest extends BaseTestCase {
    
    /**
     * Test sans circuit
     * @return void
     */
    public function testWithoutCircuit() : void
    {
        $circuitRepository =  $this->getRepository(Circuit::class);
        $list = $circuitRepository->findBySearching(null, 10, 0);
        $count = $circuitRepository->getTotalBySearching(null);

        $this->assertEquals(0, $count);
        $this->assertEquals([], $list);
    }

    /**
     * Test avec des circuits
     * @param ?string $searching Terme de la recherche
     * @param int $limit
     * @param int $offset
     * @dataProvider withCircuitsProvider
     * @return void
     */
    public function testWithCircuits(?string $searching, int $limit, int $offset) : void
    {
        $this->executeFixtures([ CircuitFixtures::class ]);

        // Données des circuits qu'on devrait obtenir
        $circuitsData = $this->getCircuitListExpected($searching);
        $expectedNumber = count($circuitsData);
        $circuitsDataExpected = array_slice($circuitsData, $offset, $limit);

        // Récupération des circuits à partir du repository
        $circuitRepository =  $this->getRepository(Circuit::class);
        $list = $circuitRepository->findBySearching($searching, $limit, $offset);
        $count = $circuitRepository->getTotalBySearching($searching);

        // Formatage des données de l'entité pour la vérification
        $listData = [];
        array_walk($list, function($circuit) use(&$listData) {
            $listData[] = [
                'id' => $circuit->getId(),
                'name' => $circuit->getName(),
                'distance' => $circuit->getDistance(),
                'city' => $circuit->getCity()->getName(),
                'state' => $circuit->getCity()->getState()->getCode(),
            ];
        });

        $this->assertEquals($expectedNumber, $count);
        $this->assertEquals($circuitsDataExpected, $listData);
    }

    /**
     * Provider pour les tests sur la liste des circuits
     * @return array
     */
    public function withCircuitsProvider() : array
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
                // Recherche, limit 8, offset 2
                'Speed', 8, 2,
            ],
            [
                // Recherche, limit 20, offset 0
                'way', 20, 0,
            ],
            [
                // Recherche complète
                'Watkins Glen International', 20, 0,
            ],
            [
                // Recherche complète sans résultat sur l'offset
                'Daytona International Speedway', 1, 1,
            ]
        );
    }

    /**
     * Retourne la liste des circuits qu'on devrait obtenir après la recherche et le tri
     * @param ?string $searching Terme de la recherche
     * @return array
     */
    private function getCircuitListExpected(?string $searching) : array
    {
        $circuitFixtures = $this->getService(CircuitFixtures::class);
        $data = $circuitFixtures->getDataFromCSV();

        // Tri les circuit par nom croissant
        usort($data, function($circuit1, $circuit2) {
            $name1 = strtolower($circuit1['name']);
            $name2 = strtolower($circuit2['name']);
            return ($name1 < $name2 ? -1 : 1);
        });

        // Filtre les circuits correspondant à la recherche
        if($searching !== null)
        {
            $data = array_filter($data, fn($item) => stripos($item['name'], $searching) !== false);
        }

        return $data;
    }
}