<?php

/**
 * Tests des contrôleurs de gestion d'un pilote
 */

namespace App\Tests\Controllers\Admin\Pilot;

use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\AbstractManageEntity;
use App\Entity\Pilot;
use App\DataFixtures\PilotFixtures;

abstract class AbstractManagePilot extends AbstractManageEntity {
    
    use WithUserCreating;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'pilot';
    }

    /**
     * Retourne le pilote dont l'identifiant public est en paramètre
     * @param string $publicId
     * @return ?Pilot
     */
    protected function getPilotByPublicId(string $publicId) : ?Pilot
    {
        $dql = sprintf('SELECT pilots FROM %s pilots WHERE pilots.public_id = :public_id', Pilot::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('public_id', $publicId)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le dernier pilote créé
     * @return ?Pilot
     */
    protected function getLastPilotCreated() : ?Pilot
    {
        $dql = sprintf('SELECT pilots FROM %s pilots ORDER BY pilots.id DESC', Pilot::class);

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
        $expectedPublicId = $params['public_id'];

        $managedPilot = $this->getPilotByPublicId($expectedPublicId);
        $this->assertNotNull($managedPilot);

        // Vérification des données
        $expectedData = [
            'public_id' => $expectedPublicId,
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'],
            'full_name' => trim(implode(' ', [
                $params['first_name'], $params['last_name'],
            ])),
            'birthdate' => $params['birthdate'],
            'birthcity' => $params['birth_city[id]'],
        ];
        $resultData = [
            'public_id' => $managedPilot?->getPublicId(),
            'first_name' => $managedPilot?->getFirstName(),
            'last_name' => $managedPilot?->getLastName(),
            'full_name' => $managedPilot?->getFullName(),
            'birthdate' => $managedPilot?->getBirthDate()->format('Y-m-d'),
            'birthcity'=> $managedPilot?->getBirthCity()->getId(),
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
                    'public_id' => 'chase-elliott',
                    'first_name' => 'Chase',
                    'last_name' => 'Elliott',
                    'birthdate' => '1995-11-28',
                    'birth_city[id]' => 36,
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
        $this->executeFixtures([ PilotFixtures::class ]);
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau pilote créé
        $pilotsData = $this->getService(PilotFixtures::class)->getDataFromCSV();
        $lastPilotExpectedData = $pilotsData[count($pilotsData) - 1];
        $lastPilotCreated = $this->getLastPilotCreated();

        $this->assertEquals($lastPilotExpectedData, [
            'id' => $lastPilotCreated?->getId(),
            'publicId' => $lastPilotCreated?->getPublicId(),
            'firstName' => $lastPilotCreated?->getFirstName(),
            'lastName' => $lastPilotCreated?->getLastName(),
            'fullName' => $lastPilotCreated?->getFullName(),
            'birthDate' => $lastPilotCreated?->getBirthDate()->format('Y-m-d'),
            'birthCity' => $lastPilotCreated?->getBirthCity()->getName(),
            'birthState' => $lastPilotCreated?->getBirthCity()->getState()->getCode(),
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
                    'public_id' => '',
                    'first_name' => '',
                    'last_name' => '',
                    'birthdate' => '',
                    'birth_city[id]'  => '',
                ], [
                    'public_id' => 'L\'identifiant public du pilote est nécessaire.',
                    'first_name' => 'Le prénom du pilote est nécessaire.',
                    'last_name' => 'Le nom du pilote est nécessaire.',
                    'birthdate' => 'La date de naissance du pilote est nécessaire.',
                    'birth_city[id]' => 'La ville de naissance du pilote est nécessaire.',
                ],
            ],
            'public_id_already_exists' => [
                // Test avec un identifiant public qui existe déjà
                [  
                    'public_id' => 'chase-elliott',
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'public_id' => 'Un pilote utilise déjà cet identifiant.',
                ],
            ],
            'public_id_too_short' => [
                // Test avec un identifiant public trop court
                [
                    'public_id' => 'chas',
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'public_id' => 'L\'identifiant public du pilote doit avoir au moins 5 caractères.',
                ],
            ],
            'public_id_too_long' => [
                // Test avec un identifiant public trop long
                [
                    'public_id' => $faker->slug(200),
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'public_id' => 'L\'identifiant public du pilote ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            'first_name_too_short'  => [
                // Test avec un prénom trop court 
                [
                    'public_id' => $faker->slug(),
                    'first_name' => 'C',
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'first_name' => 'Le prénom du pilote doit avoir au moins 2 caractères.',
                ]
            ],
            'first_name_too_long' => [
                // Test avec un prénom trop long
                [
                    'public_id' => $faker->slug(),
                    'first_name' => $faker->realTextBetween(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'first_name' => 'Le prénom du pilote ne doit pas avoir plus de 100 caractères.',
                ]
            ],
            'last_name_too_short' => [
                // Test avec un nom trop court
                [
                    'public_id' => $faker->slug(),
                    'first_name' => $faker->firstName(),
                    'last_name' => 'Ell',
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'last_name' => 'Le nom du pilote doit avoir au moins 4 caractères.',
                ]
            ],
            'last_name_too_long' => [
                // Test avec un nom trop long
                [
                    'public_id' => $faker->slug(),
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->realTextBetween(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36,
                ], [
                    'last_name' => 'Le nom du pilote ne doit pas avoir plus de 100 caractères.',
                ]
            ],
            'birthdate_incorrect' => [
                // Test avec une date de naissance incorrect
                [
                    'public_id' => $faker->slug(),
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => 'Pomme',
                    'birth_city[id]'  => 36,
                ], [
                    'birthdate' => 'La date de naissance est incorrecte.',
                ]
            ],
            'birth_city_incorrect' => [
               // Test avec une ville de naissance qui n'existe pas 
               [
                    'public_id' => $faker->slug(),
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                    'birth_city[id]'  => 36000,
               ], [
                   'birth_city[id]' => 'La ville de naissance du pilote est nécessaire.',
               ]
            ],
        );
    }

    /*****************************************************************************/

}