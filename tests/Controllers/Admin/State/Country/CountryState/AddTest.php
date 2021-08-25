<?php

/**
 * Tests du contrôleur d'ajout d'un état
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState;

final class AddTest extends AbstractManageCountryState {

     /**
     * Retourne l'URI de la page de gestion d'un pays
     * @return string
     */
    protected function manageStateUri() : string
    {
        return '/admin/countries/fr/states/add';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'L\'état :name a été ajouté au pays France.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Création d\'un état pour le pays France';
    }

    /**
     * Vérification du succès de la création d'un pays
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->createCountry('FR', 'France');

        $countBeforeCalling = $this->countStates();

        parent::testSuccess($params);

        // Vérifie que le nombre d'état a augmenté
        $this->assertEquals($countBeforeCalling + 1, $this->countStates());

        // Vérifie le code du pays de l'état
        $countryStateManaged = $this->getStateByCode($params['code']);
        $this->assertNotNull($countryStateManaged);
        $this->assertEquals('FR', $countryStateManaged->getCountry()->getCode());
    }

    /*****************************************************************************/

}