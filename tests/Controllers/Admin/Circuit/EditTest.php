<?php

/**
 * Tests du contrôleur d'édition d'un circuit
 */

namespace App\Tests\Controllers\Admin\Circuit;

use App\Entity\Circuit;
use App\DataFixtures\CircuitFixtures;

final class EditTest extends AbstractManageCircuit {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/circuits/1/edit'; // Daytona International Speedway
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        $name = $params['name'];

        return sprintf('Le circuit %s a été mis à jour.', $name);
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        $name = 'Daytona International Speedway';

        return sprintf('Modification du circuit %s', $name);
    }

    /**
     * Test lorsque le circuit n'existe pas
     * @return void
     */
    public function testNotExists() : void
    {
        $this->attemptManageEntity([]);

        $expectedTitle = 'Erreur 404';
        
        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', $expectedTitle);
        $this->assertPageTitleSame($expectedTitle);
    }

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ CircuitFixtures::class, ]);

        $lastCircuitCreatedExpected = $this->getLastCircuitCreated();

        parent::testSuccess($params);

        // Vérifie qu'il n'y a pas eu de création
        $lastCircuitCreated = $this->getLastCircuitCreated();
        $this->assertEquals($lastCircuitCreatedExpected->getId(), $lastCircuitCreated->getId());
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        parent::checkFailureEntityData($params);

        // On vérifie ici que le circuit n'a pas été mis à jour
        $circuitData = $this->getService(CircuitFixtures::class)->getDataFromCSV();
        $circuitDataExpected = current($circuitData);
        $circuitInDatabase = $this->getRepository(Circuit::class)->find($circuitDataExpected['id']);

        $cityInDatabase = $circuitInDatabase?->getCity();

        $this->assertEquals($circuitDataExpected, [
            'id' => $circuitInDatabase?->getId(),
            'name' => $circuitInDatabase?->getName(),
            'distance' => $circuitInDatabase?->getDistance(),
            'city' => $cityInDatabase?->getName(), 
            'state' => $cityInDatabase?->getState()?->getCode(),
        ]);
    }

}