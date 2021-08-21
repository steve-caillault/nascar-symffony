<?php

/**
 * Tests du contrôleur d'ajout d'un pays
 */

namespace App\Tests\Controllers\Admin\Country;

use App\Entity\Country;

final class AddTest extends AbstractManageCountry {

     /**
     * Retourne l'URI de la page de gestion d'un pays
     * @return string
     */
    protected function manageCountryUri() : string
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
     * @param ?Country $country Pays en cas d'édition
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params, ?Country $country = null) : void
    {
        $countCountriesBeforeCalling = $this->countCountries();

        parent::testSuccess($params, $country);

        // Vérifie que le nombre de pays a augmenté
        $this->assertEquals($countCountriesBeforeCalling + 1, $this->countCountries());
    }

    /*****************************************************************************/

}