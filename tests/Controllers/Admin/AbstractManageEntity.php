<?php

/**
 * Tests des contrôleurs de gestion d'une entité
 */

namespace App\Tests\Controllers\Admin;

use Symfony\Component\DomCrawler\Crawler;
/***/
use App\Tests\WithUserCreating;
use App\Tests\BaseTestCase;

abstract class AbstractManageEntity extends BaseTestCase {

    use WithUserCreating;

    /**
     * Retourne l'URI de la page de gestion de l'entité
     * @return string
     */
    abstract protected function manageUri() : string;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    abstract protected function getFormName() : string;

    /**
     * Retourne le message de succès attendu
     * @param array $params Paramètres du formulaire
     * @return string
     */
    abstract protected function getSuccessFlashMessageExpected(array $params) : string;

    /**
     * Retourne le titre de la page attendu en cas de succès
     * @return string
     */
    abstract protected function getSuccessPageTitleExpected() : string;

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    abstract protected function getFailurePageTitleExpected() : string;

    /**
     * Retourne le sélecteur du champs dont la clé du Provider est en paramètre
     * @param string $key
     * @return string
     */
    protected function getFieldKeySelector(string $key) : string
    {
        $formName = $this->getFormName();

        // Pour les champs à sous tableau
        if(str_contains($key, '['))
        {
           return $formName . implode(array_map(function($element) {
                return '[' . strtr($element, [ '[' => '', ']' => '' ]) . ']';
            }, explode('[', $key)));
        }

        return $formName . '[' . $key . ']';
    }

    /**
     * Tentative de gestion de l'entité
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    protected function attemptManageEntity(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $this->manageUri());

        try {
            $formName = $this->getFormName();
            $submitSelector = sprintf('form[name=%s] input[type=submit]', $formName);
            $submitButton = $crawler->filter($submitSelector);
            $form = $submitButton->form();

            $postParams = [];
            foreach($params as $key => $value)
            {
                $fieldKey = $this->getFieldKeySelector($key);
                $postParams[$fieldKey] = $value;
            }

            return $client->submit($form, $postParams);
        } catch(\Throwable) {
            return $crawler;
        }
    }

    /*****************************************************************************/

    /**
     * Vérification des données de l'entité après le succès
     * @param array $params Paramètres du formulaires
     */
    abstract protected function checkSuccessEntityData(array $params) : void;

    /**
     * Vérification du succès de la gestion d'une entité
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $response = $this->attemptManageEntity($params);

        // Vérification du message Flash
        $expectedFlashMessage = $this->getSuccessFlashMessageExpected($params);

        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $titleExpected = $this->getSuccessPageTitleExpected();
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        // Vérification des données
        $this->checkSuccessEntityData($params);

    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    abstract public function successProvider() : array;

    /*****************************************************************************/

    /**
     * Vérification des données de l'entité après l'échec
     * @param array $params Paramètres du formulaires
     */
    abstract protected function checkFailureEntityData(array $params) : void;

    /**
     * Vérification des erreurs
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $response = $this->attemptManageEntity($params);

        $titleExpected = $this->getFailurePageTitleExpected();
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = $this->getFieldKeySelector($field);
            $fieldSelector = '[name="' . $fieldKey . '"]';
            $error = $response->filter($fieldSelector)->closest('div.form-input')->filter('p.error')->text();
            $this->assertEquals($messageExpected, $error);
        }

        // Vérification des données
        $this->checkFailureEntityData($params);
    }

    /**
     * Provider pour les tests d'échec de la validation
     * @return array
     */
    abstract public function failureValidationProvider() : array;

    /*****************************************************************************/

}