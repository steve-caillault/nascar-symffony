<?php

/**
 * Tests des contrôleurs de gestion d'un moteur
 */

namespace App\Tests\Controllers\Admin\Motor;

use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\AbstractManageEntity;
use App\Entity\Motor;
use App\DataFixtures\MotorFixtures;

abstract class AbstractManageMotor extends AbstractManageEntity {
    
    use WithUserCreating;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'motor';
    }

    /**
     * Retourne le moteur dont l'identifiant public est en paramètre
     * @param string $publicId
     * @return ?Motor
     */
    protected function getMotorByPublicId(string $publicId) : ?Motor
    {
        $dql = sprintf('SELECT motors FROM %s motors WHERE motors.public_id = :public_id', Motor::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('public_id', $publicId)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le dernier moteur créé
     * @return ?Motor
     */
    protected function getLastMotorCreated() : ?Motor
    {
        $dql = sprintf('SELECT motors FROM %s motors ORDER BY motors.id DESC', Motor::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /*****************************************************************************/

    /**
     * Vérification des données de l'entité après le succès
     * @param array $params Paramètres du formulaire
     * @return void
     */
    protected function checkSuccessEntityData(array $params) : void
    {
        $expectedPublicId = $params['public_id'];

        $managedMotor = $this->getMotorByPublicId($expectedPublicId);
        $this->assertNotNull($managedMotor);

        // Vérification des données
        $expectedData = [
            'public_id' => $expectedPublicId,
            'name' => $params['name'],
        ];
        $resultData = [
            'public_id' => $managedMotor?->getPublicId(),
            'name' => $managedMotor?->getName(),
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
                    'public_id' => 'dodge',
                    'name' => 'Dodge',
                ],
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Vérification des erreurs de validation
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $this->executeFixtures([ MotorFixtures::class ]);
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau moteur créé
        $motorsData = $this->getService(MotorFixtures::class)->getMotorsData();
        $lastMotorExpectedData = $motorsData[count($motorsData) - 1];
        $lastMotorCreated = $this->getLastMotorCreated();

        $this->assertEquals($lastMotorExpectedData, [
            'id' => $lastMotorCreated?->getId(),
            'publicId' => $lastMotorCreated?->getPublicId(),
            'name' => $lastMotorCreated?->getName(),
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
                    'name' => '',
                ], [
                    'public_id' => 'L\'identifiant public du moteur est nécessaire.',
                    'name' => 'Le nom du moteur est requis.',
                ],
            ],
            'public_id_already_exists' => [
                // Test avec un identifiant public qui existe déjà
                [  
                    'public_id' => 'ford',
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'Un moteur utilise déjà cet identifiant.',
                ],
            ],
            'public_id_too_short' => [
                // Test avec un identifiant public trop court
                [
                    'public_id' => 'che',
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'L\'identifiant public du moteur doit avoir au moins 5 caractères.',
                ],
            ],
            'public_id_too_long' => [
                // Test avec un identifiant public trop long
                [
                    'public_id' => $faker->slug(200),
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'L\'identifiant public du moteur ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            'name_already_exists' => [
                // Test avec un nom qui existe déjà
                [  
                    'public_id' => $faker->slug(),
                    'name' => 'ford',
                ], [
                    'name' => 'Un moteur utilise déjà ce nom.',
                ],
            ],
            'name_too_short'  => [
                // Test avec un nom trop court 
                [
                    'public_id' => $faker->slug(),
                    'name' => 'Toy',
                ], [
                    'name' => 'Le moteur doit avoir au moins 4 caractères.',
                ]
            ],
            'name_too_long' => [
                // Test avec un prénom trop long
                [
                    'public_id' => $faker->slug(),
                    'name' => $faker->realTextBetween(50),
                ], [
                    'name' => 'Le moteur ne doit pas avoir plus de 20 caractères.',
                ]
            ],
        );
    }

    /*****************************************************************************/

}