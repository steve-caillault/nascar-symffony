<?php

/**
 * Tests du contrôleur d'édition d'un propriétaire
 */

namespace App\Tests\Controllers\Admin\Owner;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\DataFixtures\OwnerFixtures;
use App\Entity\{
    Owner,
    OwnerPublicIdHistory
};
use App\Tests\WithOwnerManagingTrait;

final class EditTest extends AbstractManageOwner {

    use WithOwnerManagingTrait;

    /**
     * Retourne si l'identifiant public existe pour le propriétaire en paramètre dans la table des identifiants
     * @param Owner $owner
     * @param string $publicId
     * @return bool
     */
    private function existsInPublicIdsTable(Owner $owner, string $publicId) : bool
    {
        $dql = sprintf('SELECT COUNT(owners_public_ids.public_id) FROM %s owners_public_ids WHERE owners_public_ids.owner = :owner AND owners_public_ids.public_id = :public_id', OwnerPublicIdHistory::class);

        $count = (int) $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('owner', $owner)
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
        return '/admin/owners/roush-fenway-racing/edit';
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        return sprintf('Le propriétaire %s a été mis à jour.', $params['name']);
    }

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    protected function getSuccessPageTitleExpected() : string
    {
        return 'Liste des propriétaires';
    }

    /**
     * Retourne le titre de la page d'édition
     * @return string
     */
    private function getEditPageTitle() : string
    {
        return sprintf('Modification du propriétaire %s', 'Roush Fenway Racing');
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
     * Test lorsque le propriétaire n'existe pas
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
        $this->executeFixtures([ OwnerFixtures::class, ]);

        $owner = $this->getRepository(Owner::class)->find(13);

        if($addHistory)
        {
            $this->addOwnerPublicId($owner, $publicId);
        }

        // URI finale attendu
        $uri = sprintf('/admin/owners/%s/edit', $publicId);
        $finalPublicId = ($mustFound) ? $owner->getPublicId() : $publicId;
        $expectedUri = $this->getService(UrlGeneratorInterface::class)->generate('app_admin_owners_edit_index', [
            'ownerPublicId' => $finalPublicId,
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
                'roush-fenway-racing', false, true,
            ],
            'with-history' => [
                'roush-fenway-keselowski-racing', true, true
            ],
            'not-found' => [
                'chip-ganassi-keselowski-racing', false, false,
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
        $this->executeFixtures([ OwnerFixtures::class, ]);

        parent::testSuccess($params);

        $ownersData = $this->getService(OwnerFixtures::class)->getDataFromCSV();

        // Récupére l'ancien et le nouvel identifiant public
        $oldOwnerData = $ownersData[12];
        $oldPublicId = $oldOwnerData['publicId'];
        $expectedPublicId = $params['public_id'];

        // Recherche le propriétaire édité par son identifiant public
        $ownerInDatabase = $this->getOwnerByPublicId($expectedPublicId);
        $this->assertNotNull($ownerInDatabase);

         // Vérifie que l'ancien id est présent dans la table des anciens identifiants, s'il a changé
        $oldPublicIdSaved = $this->existsInPublicIdsTable($ownerInDatabase, $oldOwnerData['publicId']);
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
                    'public_id' => 'rfkr',
                    'name' => 'Roush Fenway Keselowski Racing',
                ],
                'name-only' => [
                    'public_id' => 'roush-fenway-racing',
                    'name' => 'Roush Fenway Keselowski Racing',
                ],
                'public-id-only' => [
                    'public_id' => 'rfkr',
                    'name' => 'Roush Fenway Racing',
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
        $ownersData = $this->getService(OwnerFixtures::class)->getDataFromCSV();
        $ownerDataExpected = current($ownersData);
        $ownerInDatabase = $this->getRepository(Owner::class)->find($ownerDataExpected['id']);

        $this->assertEquals($ownerDataExpected, [
            'id' => $ownerInDatabase?->getId(),
            'publicId' => $ownerInDatabase?->getPublicId(),
            'name' => $ownerInDatabase?->getName(),
        ]);

        // On vérifie qu'il n'y a pas eu d'enregistrement dans la table des identifiants des propriétaires
        $oldPublicIdSaved = $this->existsInPublicIdsTable($ownerInDatabase, $ownerInDatabase?->getPublicId());
        $this->assertEquals(false, $oldPublicIdSaved);
    }

}