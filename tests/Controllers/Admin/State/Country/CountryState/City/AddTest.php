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

    /*****************************************************************************/

}