<?php

/**
 * Tests des contrôleurs de gestion d'un circuit
 */

namespace App\Tests\Controllers\Admin\Circuit;

use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\AbstractManageEntity;
use App\Entity\Circuit;
use App\DataFixtures\CircuitFixtures;

abstract class AbstractManageCircuit extends AbstractManageEntity {
    
    use WithUserCreating;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'circuit';
    }

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    protected function getSuccessPageTitleExpected() : string
    {
        return 'Liste des circuits';
    }

    /**
     * Retourne le circuit dont le nom est en paramètre
     * @param string $name
     * @return ?Circuit
     */
    protected function getCircuitByName(string $name) : ?Circuit
    {
        $dql = sprintf('SELECT circuits FROM %s circuits WHERE circuits.name = :name', Circuit::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le dernier $circuit créé
     * @return ?Circuit
     */
    protected function getLastCircuitCreated() : ?Circuit
    {
        $dql = sprintf('SELECT circuits FROM %s circuits ORDER BY circuits.id DESC', Circuit::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /*****************************************************************************/

    /**
     * Vérification des données de l'entité après le succès
     * @param array $params Paramètres du formulaires
     * @return void
     */
    protected function checkSuccessEntityData(array $params) : void
    {
        $expectedName = $params['name'];

        $managedCircuit = $this->getCircuitByName($expectedName);
        $this->assertNotNull($managedCircuit);

        // Vérification des données
        $expectedData = [
            'name' => $expectedName,
            'city' => $params['city[id]'],
            'distance' => $params['distance'],
        ];
        $resultData = [
            'name' => $managedCircuit?->getName(),
            'city'=> $managedCircuit?->getCity()->getId(),
            'distance' => $managedCircuit?->getDistance(),
        ];

        $this->assertEquals($expectedData, $resultData);
    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        return array(
            [
                'success' => [ 
                    'name' => 'Daytona International Speedway',
                    'city[id]' => 7,
                    'distance' => 4023,
                ],
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $this->executeFixtures([ CircuitFixtures::class ]);
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau circuit créé
        $circuitsData = $this->getService(CircuitFixtures::class)->getDataFromCSV();
        $lastCircuitExpectedData = $circuitsData[count($circuitsData) - 1];
        $lastCircuitCreated = $this->getLastCircuitCreated();

        $city = $lastCircuitCreated?->getCity();
        $state = $city?->getState();

        $this->assertEquals($lastCircuitExpectedData, [
            'id' => $lastCircuitCreated?->getId(),
            'name' => $lastCircuitCreated?->getName(),
            'distance' => $lastCircuitCreated?->getDistance(),
            'city' => $city?->getName(),
            'state' => $state?->getCode(),
        ]);
    }

    /**
     * Provider pour les tests d'échec de la validation
     * @return array
     */
    public function failureValidationProvider() : array
    {
        $faker = $this->getFaker();

        return array(
            'empty' => [
                // Test paramètres vide
                [
                    'name' => '',
                    'city[id]'  => '',
                    'distance' => '',
                ], [
                    'name' => 'Le nom du circuit est nécessaire.',
                    'city[id]' => 'La ville du circuit est nécessaire.',
                    'distance' => 'La distance du circuit est nécessaire.',
                ],
            ],
            'name_too_short' => [
                // Test avec un nom trop court
                [
                    'name' => 'Pom',
                    'city[id]'  => 5,
                    'distance' => $faker->numberBetween(200, 9999),
                ], [
                    'name' => 'Le nom du circuit doit avoir au moins 5 caractères.',
                ],
            ],
            'name_too_long' => [
                // Test avec un nom trop long
                [
                    'name' => $faker->realTextBetween(101, 200),
                    'city[id]'  => 8,
                    'distance' => $faker->numberBetween(200, 9999),
                ], [
                    'name' => 'Le nom du circuit doit avoir au plus 100 caractères.',
                ],
            ],
            'city_incorrect' => [
                // Test avec une ville qui n'existe pas 
                [
                    'name' => 'Daytona International Speedway',
                    'city[id]'  => 36000,
                    'distance' => $faker->numberBetween(200, 9999),
                ], [
                    'city[id]' => 'La ville du circuit est nécessaire.',
                ]
            ],
            'distance_is_string' => [
                // Test pour une distance qui est une chaine de caractères
                [
                    'name' => 'Charlotte Motor Speedway',
                    'city[id]'  => 32,
                    'distance' => 'Pom',
                ], [
                    'distance' => 'La distance du circuit doit être un entier.',
                ]
            ],
            'distance_is_float' => [
                // Test pour une distance qui est décimal
                [
                    'name' => 'Martinsville Speedway',
                    'city[id]'  => 15,
                    'distance' => 846.5,
                ], [
                    'distance' => 'La distance du circuit doit être un entier.',
                ]
            ],
            'distance_is_too_short' => [
                // Test pour une distance trop faible
                [
                    'name' => $faker->name(),
                    'city[id]'  => 106,
                    'distance' => $faker->numberBetween(199),
                ], [
                    'distance' => 'La distance du circuit doit avoir entre 200 et 10000 mètres.',
                ]
            ],
            'distance_is_too_long' => [
                // Test pour une distance trop longue
                [
                    'name' => $faker->name(),
                    'city[id]'  => 93,
                    'distance' => $faker->numberBetween(10001),
                ], [
                    'distance' => 'La distance du circuit doit avoir entre 200 et 10000 mètres.',
                ]
            ],
        );
    }

    /*****************************************************************************/

}