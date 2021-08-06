<?php

/**
 * Tests du contrôleur d'ajout d'une saison
 */

namespace App\Tests\Controllers\Admin\Season;

final class AddTest extends AbstractManageSeason {

     /**
     * Retourne l'URI de la page de gestion de la saison
     * @return string
     */
    protected function manageSeasonUri() : string
    {
        return '/admin/seasons/add';
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'La saison :year a été créée.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return 'Création d\'une saison';
    }

    /*****************************************************************************/

}