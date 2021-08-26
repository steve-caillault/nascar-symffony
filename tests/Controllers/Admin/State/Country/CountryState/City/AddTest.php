<?php

/**
 * Tests du contrôleur d'ajout d'une ville
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState\City;

final class AddTest extends AbstractManageCity {

     /**
     * Retourne l'URI de la page de gestion d'une ville
     * @return string
     */
    protected function manageCityUri() : string
    {
        return '/admin/countries/us/states/ia/cities/add';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'La ville :name a été ajoutée à l\'état Iowa.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Création d\'une ville pour l\'état Iowa';
    }

    /**
     * Vérification du succès de la création d'un pays
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $usCountry = $this->createCountry('US', 'États-Unis d\'Amérique');
        $this->createCountryState($usCountry, 'IA', 'Iowa');

        $countBeforeCalling = $this->countCities();

        parent::testSuccess($params);

        // Vérifie que le nombre d'état a augmenté
        $this->assertEquals($countBeforeCalling + 1, $this->countCities());
    }

    /**
     * Vérification des erreurs lors de la gestion d'une ville
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $faker = $this->getFaker();

        $usCountry = $this->createCountry('US', 'États-Unis d\'Amérique');
        $iowaState = $this->createCountryState($usCountry, 'IA', 'Iowa');

        $this->createCity($iowaState, $faker->city(), $faker->latitude(), $faker->longitude());

        parent::testValidationFailure($params, $errorsExpected);
    }

    /*****************************************************************************/

}