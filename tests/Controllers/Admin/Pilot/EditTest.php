<?php

/**
 * Tests du contrôleur d'édition d'un pilote
 */

namespace App\Tests\Controllers\Admin\Pilot;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\DataFixtures\PilotFixtures;
use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};
use App\Tests\WithPilotManagingTrait;

final class EditTest extends AbstractManagePilot {

    use WithPilotManagingTrait;

    /**
     * Retourne si l'identifiant public existe pour le pilote en paramètre dans la table des identifiants
     * @param Pilot $pilot
     * @param string $public_ic
     * @return bool
     */
    private function existsInPublicIdsTable(Pilot $pilot, string $publicId) : bool
    {
        $dql = sprintf('SELECT COUNT(pilots_public_ids.public_id) FROM %s pilots_public_ids WHERE pilots_public_ids.pilot = :pilot AND pilots_public_ids.public_id = :public_id', PilotPublicIdHistory::class);

        $count = (int) $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('pilot', $pilot)
            ->setParameter('public_id', $publicId)
            ->getSingleScalarResult()
        ;

        return ($count === 1);
    }

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/pilots/jeffrey-earnhardt/edit';
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

        return sprintf('Le pilote %s a été mis à jour.', $fullName);
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
     * Retourne le titre de la page d'édition
     * @return string
     */
    private function getEditPageTitle() : string
    {
        return sprintf('Modification du pilote %s', 'Jeffrey Earnhardt');
    }

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    protected function getFailurePageTitleExpected() : string
    {
        return $this->getEditPageTitle();
    }

    /**
     * Test lorsque le pilote n'existe pas
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
     * Vérification de la redirection lors de l'utilisation d'un ancien identifiant public 
     * @param string $publicId
     * @param bool $addHistory Vrai s'il faut ajouter l'identifiant public à l'historique
     * @param bool $mustFound Vrai si la page doit être accessible
     * @dataProvider publidIdsProvider
     * @return void
     */
    public function testPublicIds(string $publicId, bool $addHistory, bool $mustFound) : void
    {
        $this->executeFixtures([ PilotFixtures::class, ]);

        $pilot = $this->getRepository(Pilot::class)->find(1);

        if($addHistory)
        {
            $this->addPilotPublicId($pilot, $publicId);
        }

        // URI finale attendu
        $uri = sprintf('/admin/pilots/%s/edit', $publicId);
        $finalPublicId = ($mustFound) ? $pilot->getPublicId() : $publicId;
        $expectedUri = $this->getService(UrlGeneratorInterface::class)->generate('app_admin_pilots_edit_index', [
            'pilotPublicId' => $finalPublicId,
        ]);

        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $uri);

        $statusExpected = ($mustFound) ? 200 : 404;
        $titleExpected = ($mustFound) ? $this->getEditPageTitle() : 'Erreur 404';
        $this->assertResponseStatusCodeSame($statusExpected);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);
        $this->assertTrue(str_contains($crawler->getUri(), $expectedUri));
    }

     /**
     * Provider pour la recherche de pilote par identifiant public
     * @return array
     */
    public function publidIdsProvider() : array
    {
        return [
            'without-history' => [
                'jeffrey-earnhardt', false, true,
            ],
            'with-history' => [
                'jeffrey-earnhardt-2', true, true
            ],
            'not-found' => [
                'jeffrey-earnhardt-2', false, false,
            ]
        ];
    }


    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ PilotFixtures::class, ]);

        parent::testSuccess($params);

        $pilotsData = $this->getService(PilotFixtures::class)->getDataFromCSV();

        // Récupére l'ancien et le nouvel identifiant public
        $oldPilotData = current($pilotsData);
        $oldPublicId = $oldPilotData['publicId'];
        $expectedPublicId = $params['public_id'];

        // Recherche le pilote édité par son identifiant public
        $pilotInDatabase = $this->getPilotByPublicId($expectedPublicId);
        $this->assertNotNull($pilotInDatabase);

         // Vérifie que l'ancien id est présent dans la table des anciens identifiants, s'il a changé
        $oldPublicIdSaved = $this->existsInPublicIdsTable($pilotInDatabase, $oldPilotData['publicId']);
        $expectedAdded = ($oldPublicId !== $expectedPublicId);
        $this->assertEquals($expectedAdded, $oldPublicIdSaved);
    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        return array(
            [
                'success' => [ 
                    'public_id' => 'nouveau-pilot',
                    'first_name' => 'Nouveau',
                    'last_name' => 'Pilote',
                    'birthdate' => '1979-03-10',
                    'birth_city[id]' => 54,
                ],
            ],
        );
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        parent::checkFailureEntityData($params);

        // On vérifie ici que le pilote n'a pas été mis à jour
        $pilotsData = $this->getService(PilotFixtures::class)->getDataFromCSV();
        $pilotDataExpected = current($pilotsData);
        $pilotInDatabase = $this->getRepository(Pilot::class)->find($pilotDataExpected['id']);

        $this->assertEquals($pilotDataExpected, [
            'id' => $pilotInDatabase?->getId(),
            'publicId' => $pilotInDatabase?->getPublicId(),
            'firstName' => $pilotInDatabase?->getFirstName(),
            'lastName' => $pilotInDatabase?->getLastName(),
            'fullName' => $pilotInDatabase?->getFullName(),
            'birthDate' => $pilotInDatabase?->getBirthDate()->format('Y-m-d'),
            'birthCity' => $pilotInDatabase?->getBirthCity()->getName(),
            'birthState' => $pilotInDatabase?->getBirthCity()->getState()->getCode(),
        ]);

        // On vérifie qu'il n'y a pas eu d'enregistrement dans la table des identifiants des pilotes
        $oldPublicIdSaved = $this->existsInPublicIdsTable($pilotInDatabase, $pilotInDatabase?->getPublicId());
        $this->assertEquals(false, $oldPublicIdSaved);
    }

}