<?php

/**
 * Tests des contrôleurs de gestion d'un état d'un pays
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState;

use App\Tests\Controllers\Admin\State\{
    AbstractManageState,
    WithCountryStateCreation
};
use App\Entity\CountryState;

abstract class AbstractManageCountryState extends AbstractManageState {

    use WithCountryStateCreation;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    protected function getFormName() : string
    {
        return 'country_state';
    }

    /**
     * Retourne le titre de la page de redirection en cas de succès
     * @return string
     */
    protected function getSuccessPageTitle() : string
    {
        return 'Liste des états du pays France';
    }

    /**
     * Nom de la classe de l'entité de l'état à utiliser
     * @return string
     */
    protected function getStateEntityClass() : string
    {
        return CountryState::class;
    }

    /**
     * Retourne le répertoire où sont stockées les images des drapeaux
     * @return string
     */
    protected function getImagesDirectory() : string
    {
        return 'images/states/';
    }

    /*****************************************************************************/

    /**
     * Test pour un pays qui n'existe pas
     * @return void
     */
    public function testCountryNotExists() : void
    {
        $this->attemptManageState([]);

        $expectedTitle = 'Erreur 404';
        
        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', $expectedTitle);
        $this->assertPageTitleSame($expectedTitle);
    }

    /**
     * Vérification des erreurs de validation
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $country = $this->createCountry('FR', 'France', 'fr.png');
        $this->createCountryState($country, 'FR', 'France', 'fr.png');
        $this->createCountryState($country, 'FRS', 'France State', 'frs.png');
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

        // Test d'un état déjà existant
        $data['entity_already_exists'] = [
            [
                'code' => 'frs',
                'name' => $faker->country(),
            ], [
                'code' => 'L\'état "FRS" existe déjà.',
            ],
        ];
        // Test si le code ISO est trop court
        $data['iso_code_too_short'] = [
            [
                'code' => 'f',
                'name' => $faker->country(),
            ], [
                'code' => 'Le code ISO doit avoir au moins 2 caractères.',
            ],
        ];
        // Test si le code ISO est trop long
        $data['iso_code_too_long'] = [
            [
                'code' => 'frfr',
                'name' => $faker->country(),
            ], [
                'code' => 'Le code ISO ne doit pas avoir plus de 3 caractères.',
            ],
        ];

        return $data;
    }

    /*****************************************************************************/

}