<?php

/**
 * Tests des contrôleurs de gestion d'un modèle de voiture
 */

namespace App\Tests\Controllers\Admin\Car;

use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\AbstractManageEntity;
use App\Entity\CarModel;
use App\DataFixtures\CarModelFixtures;

abstract class AbstractManageCar extends AbstractManageEntity {
    
    use WithUserCreating;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'car_model';
    }

    /**
     * Retourne le modèle de voiture dont le nom est en paramètre
     * @param string $name
     * @return ?CarModel
     */
    protected function getCarModelByName(string $name) : ?CarModel
    {
        $dql = sprintf('SELECT cars FROM %s cars WHERE cars.name = :name', CarModel::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le dernier modèle de voiture créé
     * @return ?CarModel
     */
    protected function getLastCarModelCreated() : ?CarModel
    {
        $dql = sprintf('SELECT cars FROM %s cars ORDER BY cars.id DESC', CarModel::class);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    protected function getSuccessPageTitleExpected() : string
    {
        return 'Liste des modèles de voiture';
    }

    /*****************************************************************************/

    /**
     * Vérification des données de l'entité après le succès
     * @param array $params Paramètres du formulaire
     * @return void
     */
    protected function checkSuccessEntityData(array $params) : void
    {
        $expectedName = $params['name'];

        $managedCarModel = $this->getCarModelByName($expectedName);
        $this->assertNotNull($managedCarModel);

        // Vérification des données
        $expectedData = [
            'name' => $params['name'],
            'motor' => $params['motor[id]'],
        ];
        $resultData = [
            'name' => $managedCarModel?->getName(),
            'motor' => $managedCarModel?->getMotor()->getId(),
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
                    'name' => 'Chevrolet Camaro 2022',
                    'motor[id]' => 1,
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
        $this->executeFixtures([ CarModelFixtures::class ]);
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau modèle de voiture créé
        $carModelsData = $this->getService(CarModelFixtures::class)->getCarModelsData();
        $lastCarModelExpectedData = $carModelsData[count($carModelsData) - 1];
        $lastCarModelCreated = $this->getLastCarModelCreated();

        $this->assertEquals($lastCarModelExpectedData, [
            'id' => $lastCarModelCreated?->getId(),
            'name' => $lastCarModelCreated?->getName(),
            'motor' => $lastCarModelCreated?->getMotor()->getId(),
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
                    'motor[id]' => '',
                ], [
                    'name' => 'Le nom du modèle est nécessaire.',
                    'motor[id]' => 'Le moteur est nécessaire.',
                ],
            ],

            'name_already_exists' => [
                // Test avec un nom qui existe déjà
                [  
                    'name' => 'Ford Fusion',
                    'motor[id]' => 3,
                ], [
                    'name' => 'Ce nom est utilisé par un autre modèle.',
                ],
            ],

            'motor_incorrect' => [
                // Test avec un identifiant de moteur incorrect
                [  
                    'name' => $faker->name(),
                    'motor[id]' => 100,
                ], [
                    'motor[id]' => 'Le moteur est nécessaire.',
                ],
            ],

            'name_too_short'  => [
                // Test avec un nom trop court 
                [
                    'name' => 'Toy',
                    'motor[id]' => 2,
                ], [
                    'name' => 'Le nom du modèle doit avoir au moins 4 caractères.',
                ]
            ],

            'name_too_long' => [
                // Test avec un nom trop long
                [
                    'name' => $faker->realTextBetween(150),
                ], [
                    'name' => 'Le nom du modèle ne doit pas avoir plus de 100 caractères.',
                ]
            ],
        );
    }

    /*****************************************************************************/

}