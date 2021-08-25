<?php

/**
 * Tests du contrôleur d'édition d'un pays
 */

namespace App\Tests\Controllers\Admin\State\Country;

use App\Entity\Country;

final class EditTest extends AbstractManageCountry {

     /**
     * Retourne l'URI de la page de gestion d'un pays
     * @return string
     */
    protected function manageStateUri() : string
    {
        return '/admin/countries/gb/edit';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'Le pays :name a été mis à jour.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Modification du pays Royaume-Uni';
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
     * Vérification du succès de l'édition d'un pays
     * @param array Paramètres du formulaire
     * @param ?Country $country Pays en cas d'édition
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params, ?Country $country = null) : void
    {
        $country = $this->createCountry('gb', 'Royaume-Uni', 'gb.png');

        $countCountriesBeforeCalling = $this->countStates();

        parent::testSuccess($params, $country);

        // Vérifie que le nombre de pays n'a pas augmenté
        $this->assertEquals($countCountriesBeforeCalling, $this->countStates());
    }

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $country = $this->createCountry('gb', 'Royaume-Uni', 'gb.png');

        parent::testValidationFailure($params, $errorsExpected);

        // Vérifie que le pays n'a pas été mis à jour
        $this->assertEquals('GB', $country->getCode());
        $this->assertEquals('Royaume-Uni', $country->getName());
        $this->assertEquals('gb.png', $country->getImage());
    }

    /*****************************************************************************/

}