<?php

/**
 * Tests du contrôleur de création d'un circuit
 */

namespace App\Tests\Controllers\Admin\Circuit;

use App\DataFixtures\{
    CityFixtures,
    CircuitFixtures
};

final class AddTest extends AbstractManageCircuit {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/circuits/add';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        $name = $params['name'];

        return sprintf('Le circuit %s a été créé.', $name);
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return 'Création d\'un circuit';
    }

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ CityFixtures::class, ]);
        parent::testSuccess($params);
    }

}