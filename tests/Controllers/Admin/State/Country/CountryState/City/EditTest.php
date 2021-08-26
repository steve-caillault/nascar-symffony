<?php

/**
 * Tests du contrôleur d'édition d'une ville
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState\City;

use App\Entity\City;

final class EditTest extends AbstractManageCity {

     /**
     * Retourne l'URI de la page de gestion d'une ville
     * @return string
     */
    protected function manageCityUri() : string
    {
        return '/admin/countries/us/states/ia/cities/1/edit';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'La ville :name a été mise à jour.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Modification de la ville Cedar Rapids';
    }

    /*****************************************************************************/

    /**
     * Vérification du succès de l'édition d'un état
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $country = $this->createCountry('US', 'États-Unis d\'Amérique');
        $countryState = $this->createCountryState($country, 'IA', 'Iowa');
        $this->createCity($countryState, 'Cedar Rapids', 41.9781, -91.6627);

        $countBeforeCalling = $this->countCities();

        parent::testSuccess($params);

        // Vérifie que le nombre de villes n'a pas augmenté
        $this->assertEquals($countBeforeCalling, $this->countCities());
    }

    /**
     * Vérification des erreurs lors de la gestion d'une ville
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $usCountry = $this->createCountry('US', 'États-Unis d\'Amérique');
        $iowaState = $this->createCountryState($usCountry, 'IA', 'Iowa');
        $cityManaged = $this->createCity($iowaState, 'Cedar Rapids', 41.9781, -91.6627);

        parent::testValidationFailure($params, $errorsExpected);

        $cityManaged = $this->getRepository(City::class)->find(1);

        // Vérifie que la ville n'a pas été mise à jour
        $this->assertEquals('IA', $cityManaged?->getState()?->getCode());
        $this->assertEquals('Cedar Rapids', $cityManaged?->getName());
        $this->assertEqualsWithDelta(41.9781, $cityManaged?->getLatitude(), 0.001);
        $this->assertEqualsWithDelta(-91.6627, $cityManaged?->getLongitude(), 0.001);
    }

    /*****************************************************************************/

    /**
     * Test si la ville n'appartient pas à l'état
     * @return void
     */
    public function testCityNotBelongState() : void
    {
        $usCountry = $this->createCountry('US', 'États-Unis d\'Amérique');
        $iowaState = $this->createCountryState($usCountry, 'IA', 'Iowa');
        // Aberration, mais nécessaire pour le test
        $kansasCountry = $this->createCountryState($usCountry, 'KS', 'Kansas');

        $this->createCity($kansasCountry, 'Cedar Rapids', 41.9781, -91.6627);


        $this->checkNotFoundCityCalling();
    }

    /*****************************************************************************/

}