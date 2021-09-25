<?php

/**
 * Tests du contrôleur de création d'un pilote
 */

namespace App\Tests\Controllers\Admin\Pilot;

use App\DataFixtures\{
    CityFixtures,
    PilotFixtures
};

final class AddTest extends AbstractManagePilot {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/pilots/add';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        $fullName = trim(implode(' ', [
            $params['first_name'], $params['last_name'],
        ]));

        return sprintf('Le pilote %s a été créé.', $fullName);
    }

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    protected function getSuccessPageTitleExpected() : string
    {
        return 'Liste des pilotes';
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return 'Création d\'un pilote';
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

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        // On vérifie ici qu'il n'y a pas eu de nouveau pilote créé
        $pilotsData = $this->getService(PilotFixtures::class)->getDataFromCSV();
        $lastPilotExpectedData = $pilotsData[count($pilotsData) - 1];
        $lastPilotCreated = $this->getLastPilotCreated();

        $this->assertEquals($lastPilotExpectedData, [
            'id' => $lastPilotCreated?->getId(),
            'publicId' => $lastPilotCreated?->getPublicId(),
            'firstName' => $lastPilotCreated?->getFirstName(),
            'lastName' => $lastPilotCreated?->getLastName(),
            'fullName' => $lastPilotCreated?->getFullName(),
            'birthDate' => $lastPilotCreated?->getBirthDate()->format('Y-m-d'),
            'birthCity' => $lastPilotCreated?->getBirthCity()->getName(),
            'birthState' => $lastPilotCreated?->getBirthCity()->getState()->getCode(),
        ]);
    }

}