<?php

/**
 * Tests du contrôleur d'édition d'un moteur
 */

namespace App\Tests\Controllers\Admin\Motor;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\DataFixtures\MotorFixtures;
use App\Entity\{
    Motor,
    MotorPublicIdHistory
};
use App\Tests\WithMotorManagingTrait;

final class EditTest extends AbstractManageMotor {

    use WithMotorManagingTrait;

    /**
     * Retourne si l'identifiant public existe pour le moteur en paramètre dans la table des identifiants
     * @param Motor $motor
     * @param string $publicId
     * @return bool
     */
    private function existsInPublicIdsTable(Motor $motor, string $publicId) : bool
    {
        $dql = sprintf('SELECT COUNT(motors_public_ids.public_id) FROM %s motors_public_ids WHERE motors_public_ids.motor = :motor AND motors_public_ids.public_id = :public_id', MotorPublicIdHistory::class);

        $count = (int) $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('motor', $motor)
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
        return '/admin/motors/chevrolet/edit';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        return sprintf('Le moteur %s a été mis à jour.', $params['name']);
    }

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    protected function getSuccessPageTitleExpected() : string
    {
        return 'Liste des moteurs';
    }

    /**
     * Retourne le titre de la page d'édition
     * @return string
     */
    private function getEditPageTitle() : string
    {
        return sprintf('Modification du moteur %s', 'Chevrolet');
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
     * Test lorsque le moteur n'existe pas
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
        $this->executeFixtures([ MotorFixtures::class, ]);

        $motor = $this->getRepository(Motor::class)->find(1);

        if($addHistory)
        {
            $this->addMotorPublicId($motor, $publicId);
        }

        // URI finale attendu
        $uri = sprintf('/admin/motors/%s/edit', $publicId);
        $finalPublicId = ($mustFound) ? $motor->getPublicId() : $publicId;
        $expectedUri = $this->getService(UrlGeneratorInterface::class)->generate('app_admin_motors_edit_index', [
            'motorPublicId' => $finalPublicId,
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
     * Provider pour la recherche de moteur par identifiant public
     * @return array
     */
    public function publidIdsProvider() : array
    {
        return [
            'without-history' => [
                'chevrolet', false, true,
            ],
            'with-history' => [
                'chevrolet-2022', true, true
            ],
            'not-found' => [
                'chevrolet-2022', false, false,
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
        $this->executeFixtures([ MotorFixtures::class, ]);

        parent::testSuccess($params);

        $motorsData = $this->getService(MotorFixtures::class)->getMotorsData();

        // Récupére l'ancien et le nouvel identifiant public
        $oldMotorData = current($motorsData);
        $oldPublicId = $oldMotorData['publicId'];
        $expectedPublicId = $params['public_id'];

        // Recherche le moteur édité par son identifiant public
        $motorInDatabase = $this->getMotorByPublicId($expectedPublicId);
        $this->assertNotNull($motorInDatabase);

         // Vérifie que l'ancien id est présent dans la table des anciens identifiants, s'il a changé
        $oldPublicIdSaved = $this->existsInPublicIdsTable($motorInDatabase, $oldMotorData['publicId']);
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
                    'public_id' => 'dodge',
                    'name' => 'Dodge',
                ],
                'name-only' => [
                    'public_id' => 'chevrolet',
                    'name' => 'Chevy',
                ],
                'public-id-only' => [
                    'public_id' => 'chevy',
                    'name' => 'Chevrolet',
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

        // On vérifie ici que le moteur n'a pas été mis à jour
        $motorsData = $this->getService(MotorFixtures::class)->getMotorsData();
        $motorDataExpected = current($motorsData);
        $motorInDatabase = $this->getRepository(Motor::class)->find($motorDataExpected['id']);

        $this->assertEquals($motorDataExpected, [
            'id' => $motorInDatabase?->getId(),
            'publicId' => $motorInDatabase?->getPublicId(),
            'name' => $motorInDatabase?->getName(),
        ]);

        // On vérifie qu'il n'y a pas eu d'enregistrement dans la table des identifiants des moteurs
        $oldPublicIdSaved = $this->existsInPublicIdsTable($motorInDatabase, $motorInDatabase?->getPublicId());
        $this->assertEquals(false, $oldPublicIdSaved);
    }

}