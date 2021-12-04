<?php

/**
 * Tests du contrôleur de création d'un moteur
 */

namespace App\Tests\Controllers\Admin\Motor;

use App\DataFixtures\MotorFixtures;

final class AddTest extends AbstractManageMotor {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/motors/add';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        return sprintf('Le moteur %s a été créé.', $params['name']);
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return 'Création d\'un moteur';
    }

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ MotorFixtures::class, ]);
        parent::testSuccess($params);
    }

}