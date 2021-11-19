<?php

/**
 * Tests des contrôleurs de gestion d'un propriétaire
 */

namespace App\Tests\Controllers\Admin\Owner;

use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\AbstractManageEntity;
use App\Entity\Owner;
use App\DataFixtures\OwnerFixtures;

abstract class AbstractManageOwner extends AbstractManageEntity {
    
    use WithUserCreating;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'owner';
    }

    /**
     * Retourne le propriétaire dont l'identifiant public est en paramètre
     * @param string $publicId
     * @return ?Owner
     */
    protected function getOwnerByPublicId(string $publicId) : ?Owner
    {
        $dql = sprintf('SELECT owners FROM %s owners WHERE owners.public_id = :public_id', Owner::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('public_id', $publicId)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le dernier propriétaire créé
     * @return ?Owner
     */
    protected function getLastOwnerCreated() : ?Owner
    {
        $dql = sprintf('SELECT owners FROM %s owners ORDER BY owners.id DESC', Owner::class);

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

        $managedOwner = $this->getOwnerByPublicId($expectedPublicId);
        $this->assertNotNull($managedOwner);

        // Vérification des données
        $expectedData = [
            'public_id' => $expectedPublicId,
            'name' => $params['name'],
        ];
        $resultData = [
            'public_id' => $managedOwner?->getPublicId(),
            'name' => $managedOwner?->getName(),
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
                    'public_id' => 'roush-fenway-keselowski-racing',
                    'name' => 'Roush Fenway Keselowski Racing',
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
        $this->executeFixtures([ OwnerFixtures::class ]);
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau propriétaire créé
        $ownersData = $this->getService(OwnerFixtures::class)->getDataFromCSV();
        $lastOwnerExpectedData = $ownersData[count($ownersData) - 1];
        $lastOwnerCreated = $this->getLastOwnerCreated();

        $this->assertEquals($lastOwnerExpectedData, [
            'id' => $lastOwnerCreated?->getId(),
            'publicId' => $lastOwnerCreated?->getPublicId(),
            'name' => $lastOwnerCreated?->getName(),
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
                    'public_id' => 'L\'identifiant public du propriétaire est nécessaire.',
                    'name' => 'Le nom du propriétaire est requis.',
                ],
            ],
            'public_id_already_exists' => [
                // Test avec un identifiant public qui existe déjà
                [  
                    'public_id' => 'chip-ganassi-racing',
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'Un propriétaire utilise déjà cet identifiant.',
                ],
            ],
            'public_id_too_short' => [
                // Test avec un identifiant public trop court
                [
                    'public_id' => 'rf',
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'L\'identifiant public du propriétaire doit avoir au moins 3 caractères.',
                ],
            ],
            'public_id_too_long' => [
                // Test avec un identifiant public trop long
                [
                    'public_id' => $faker->slug(200),
                    'name' => $faker->name(),
                ], [
                    'public_id' => 'L\'identifiant public du propriétaire ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            'name_already_exists' => [
                // Test avec un nom qui existe déjà
                [  
                    'public_id' => $faker->slug(),
                    'name' => 'Wood Brothers Racing',
                ], [
                    'name' => 'Un propriétaire utilise déjà ce nom.',
                ],
            ],
            'name_too_short'  => [
                // Test avec un nom trop court 
                [
                    'public_id' => $faker->slug(),
                    'name' => 'RF',
                ], [
                    'name' => 'Le propriétaire doit avoir au moins 3 caractères.',
                ]
            ],
            'name_too_long' => [
                // Test avec un prénom trop long
                [
                    'public_id' => $faker->slug(),
                    'name' => $faker->realTextBetween(50),
                ], [
                    'name' => 'Le propriétaire ne doit pas avoir plus de 100 caractères.',
                ]
            ],
        );
    }

    /*****************************************************************************/

}