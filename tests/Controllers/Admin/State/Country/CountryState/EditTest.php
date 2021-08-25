<?php

/**
 * Tests du contrôleur d'édition d'un état
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState;

final class EditTest extends AbstractManageCountryState {

     /**
     * Retourne l'URI de la page de gestion d'un état
     * @return string
     */
    protected function manageStateUri() : string
    {
        return '/admin/countries/fr/states/fr/edit';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'L\'état :name a été mis à jour.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Modification de l\'état France';
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
        $country = $this->createCountry('fr', 'France', 'fr.png');
        $this->createCountryState($country, 'fr', 'France', 'fr.png');

        $countBeforeCalling = $this->countStates();

        parent::testSuccess($params);

        // Vérifie que le nombre d'état n'a pas augmenté
        $this->assertEquals($countBeforeCalling, $this->countStates());

        // Vérifie le code du pays de l'état
        $countryStateManaged = $this->getStateByCode($params['code']);
        $this->assertNotNull($countryStateManaged);
        $this->assertEquals('FR', $countryStateManaged->getCountry()->getCode());
    }

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        parent::testValidationFailure($params, $errorsExpected);

        $countryStateManaged = $this->getStateByCode('FR');
 
        // Vérifie que l'état n'a pas été mis à jour
        $this->assertEquals('FR', $countryStateManaged->getCountry()->getCode());
        $this->assertEquals('FR', $countryStateManaged->getCode());
        $this->assertEquals('France', $countryStateManaged->getName());
        $this->assertEquals('fr.png', $countryStateManaged->getImage());
    }

    /*****************************************************************************/

}