<?php

/**
 * Tests du contrôleur d'édition d'un modèle de voiture
 */

namespace App\Tests\Controllers\Admin\Car;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\DataFixtures\CarModelFixtures;
use App\Entity\{
    Motor,
    CarModel
};

final class EditTest extends AbstractManageCar {

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    protected function manageUri() : string
    {
        return '/admin/cars/1/edit'; // Chevrolet Camaro ZL1
    }

    /**
     * Retourne le message de succès attendu
     * @return string
     */
    protected function getSuccessFlashMessageExpected(array $params) : string
    {
        return sprintf('Le modèle de voiture %s a été mis à jour.', $params['name']);
    }

    /**
     * Retourne le titre de la page d'édition
     * @return string
     */
    private function getEditPageTitle() : string
    {
        return sprintf('Modification du modèle de voiture %s', 'Chevrolet Camaro ZL1');
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
     * Test lorsque le modèle de voiture n'existe pas
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
        $this->executeFixtures([ CarModelFixtures::class, ]);

        parent::testSuccess($params);
    }

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    protected function checkFailureEntityData(array $params) : void
    {
        parent::checkFailureEntityData($params);

        // On vérifie ici que le modèle de voiture n'a pas été mis à jour
        $carsModelsData = $this->getService(CarModelFixtures::class)->getCarModelsData();
        $carDataExpected = current($carsModelsData);
        $carModelInDatabase = $this->getRepository(CarModel::class)->find($carDataExpected['id']);

        $this->assertEquals($carDataExpected, [
            'id' => $carModelInDatabase?->getId(),
            'motor' => $carModelInDatabase?->getMotor()->getId(),
            'name' => $carModelInDatabase?->getName(),
        ]);
    }

}