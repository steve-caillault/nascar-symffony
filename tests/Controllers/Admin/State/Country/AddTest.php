<?php

/**
 * Tests du contrôleur d'ajout d'un pays
 */

namespace App\Tests\Controllers\Admin\State\Country;

final class AddTest extends AbstractManageCountry {

     /**
     * Retourne l'URI de la page de gestion d'un pays
     * @return string
     */
    protected function manageStateUri() : string
    {
        return '/admin/countries/add';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'Le pays :name a été créé.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Création d\'un pays';
    }

    /**
     * Vérification du succès de la création d'un pays
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $countCountriesBeforeCalling = $this->countStates();

        parent::testSuccess($params);

        // Vérifie que le nombre de pays a augmenté
        $this->assertEquals($countCountriesBeforeCalling + 1, $this->countStates());
    }

    /*****************************************************************************/

}