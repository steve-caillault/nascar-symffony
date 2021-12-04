<?php

/**
 * Tests du contrôleur de création d'un modèle de voiture
 */

namespace App\Tests\Controllers\Admin\Car;

use App\DataFixtures\CarModelFixtures;

final class AddTest extends AbstractManageCar {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/cars/add';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        $name = $params['name'];

        return sprintf('Le modèle de voiture %s a été créé.', $name);
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return 'Création d\'un modèle de voiture';
    }

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ CarModelFixtures::class, ]);
        parent::testSuccess($params);
    }

}