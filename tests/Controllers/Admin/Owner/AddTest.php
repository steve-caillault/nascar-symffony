<?php

/**
 * Tests du contrôleur de création d'un propriétaire
 */

namespace App\Tests\Controllers\Admin\Owner;

use App\DataFixtures\OwnerFixtures;

final class AddTest extends AbstractManageOwner {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/owners/add';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        return sprintf('Le propriétaire %s a été créé.', $params['name']);
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return 'Création d\'un propriétaire';
    }

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ OwnerFixtures::class, ]);
        parent::testSuccess($params);
    }

}