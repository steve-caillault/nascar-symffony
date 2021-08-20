<?php

/**
 * Tests du contrôleur d'ajout d'un pays
 */

namespace App\Tests\Controllers\Admin\Country;

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

    /*****************************************************************************/

}