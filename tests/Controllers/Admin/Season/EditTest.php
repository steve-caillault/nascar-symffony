<?php

/**
 * Tests du contrôleur d'édition d'une saison
 */

namespace App\Tests\Controllers\Admin\Season;

final class EditTest extends AbstractManageSeason {

     /**
     * Retourne l'URI de la page de gestion de la saison
     * @return string
     */
    protected function manageSeasonUri() : string
    {
        return strtr('/admin/seasons/:year/edit', [
            ':year' => date('Y'),
        ]);
    }

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    protected function getSuccessMessageTemplate() : string
    {
        return 'La saison :year a été mise à jour.';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailureExpectedPageTitle() : string
    {
        return strtr('Modification de la saison :year', [
            ':year' => date('Y'),
        ]);
    }

    /*****************************************************************************/

    /**
     * Test pour une saison qui n'existe pas
     * @return void
     */
    public function testSeasonNotExists() : void
    {
        $this->attemptManageSeason([]);

        $expectedTitle = 'Erreur 404';
        
        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', $expectedTitle);
        $this->assertPageTitleSame($expectedTitle);
    }

    /**
     * Vérification du succès de la création d'une saison
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $year = (int) date('Y');
        $this->createSeason($year, 'ACTIVE');
        parent::testSuccess($params);
    }

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $year = (int) date('Y');
        $this->createSeason($year, 'ACTIVE');
        $season = $this->getSeasonByYear($year);

        parent::testValidationFailure($params, $errorsExpected);

        // Vérifie que la saison n'a pas été mise à jour
        $this->assertEquals($year, $season->getYear());
        $this->assertEquals('ACTIVE', $season->getState());
    }

    /*****************************************************************************/

}