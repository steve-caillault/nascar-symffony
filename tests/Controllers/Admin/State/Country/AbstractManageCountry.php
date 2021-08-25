<?php

/**
 * Tests des contrôleurs de gestion d'un pays
 */

namespace App\Tests\Controllers\Admin\State\Country;

use App\Tests\Controllers\Admin\State\AbstractManageState;
use App\Entity\Country;

abstract class AbstractManageCountry extends AbstractManageState {

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'country';
    }

    /**
     * Retourne le titre de la page de redirection en cas de succès
     * @return string
     */
    protected function getSuccessPageTitle() : string
    {
        return 'Liste des pays';
    }

    /**
     * Nom de la classe de l'entité de l'état à utiliser
     * @return string
     */
    protected function getStateEntityClass() : string
    {
        return Country::class;
    }

     /**
     * Retourne le répertoire où sont stockées les images des drapeaux
     * @return string
     */
    protected function getImagesDirectory() : string
    {
        return 'images/countries/';
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
        $this->createCountry('FR', 'France');
        parent::testValidationFailure($params, $errorsExpected);
    }

    /**
     * Provider pour les tests d'échec de la validation
     * @return array
     */
    public function failureValidationProvider() : array
    {
        $faker = $this->getFaker();
        $data = parent::failureValidationProvider();

        // Test d'un pays existant
        $data['entity_already_exists'] = [
            [
                'code' => 'fr',
                'name' => $faker->country(),
            ], [
                'code' => 'Le pays "FR" existe déjà.',
            ],
        ];
        // Test si le code ISO est trop court
        $data['iso_code_too_short'] = [
            [
                'code' => 'f',
                'name' => $faker->country(),
            ], [
                'code' => 'Le code ISO doit être formé de deux lettres.',
            ],
        ];
        // Test si le code ISO est trop long
        $data['iso_code_too_long'] = [
            [
                'code' => 'frr',
                'name' => $faker->country(),
            ], [
                'code' => 'Le code ISO doit être formé de deux lettres.',
            ],
        ];

        return $data;
    }

    /*****************************************************************************/

}