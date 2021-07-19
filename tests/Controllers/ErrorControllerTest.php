<?php

/**
 * Test du contrôleur d'erreur
 */

namespace App\Tests\Controllers;

use App\Tests\BaseTestCase;

final class ErrorControllerTest extends BaseTestCase {
    
    /**
     * Test erreur 401
     * @return void
     */
    public function testUnauthorized() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/testing/error-401');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Vous devez être identifié pour accéder à cette page.';

        $this->assertResponseStatusCodeSame(401);
        $this->assertPageTitleSame('Erreur 401');
        $this->assertSelectorTextContains('h1', 'Erreur 401');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Test erreur 401 en Ajax
     * @return void
     */
    public function testUnauthorizedAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/error-401/ajax');

        $response = $client->getResponse()->getContent();
        $expected = json_encode([
            'status' => 'ERROR',
            'data' => [
                'code' => 401,
                'message' => 'Vous devez être identifié pour accéder à cette page.',
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals($expected, $response);
    }

    /**
     * Test erreur 403
     * @return void
     */
    public function testForbidden() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/testing/error-403');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Vous n\'êtes pas autorisé à accéder à cette page.';

        $this->assertResponseStatusCodeSame(403);
        $this->assertPageTitleSame('Erreur 403');
        $this->assertSelectorTextContains('h1', 'Erreur 403');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Test erreur 403 en Ajax
     * @return void
     */
    public function testForbiddenAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/error-403/ajax');

        $response = $client->getResponse()->getContent();
        $expected = [
            'status' => 'ERROR',
            'data' => [
                'code' => 403,
                'message' => 'Vous n\'êtes pas autorisé à accéder à cette page.',
            ],
        ];

        $this->assertResponseStatusCodeSame(403);
        $this->assertEquals($expected, json_decode($response, true));
    }

    /**
     * Test erreur 404
     * @return void
     */
    public function testNotFound() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/testing/error-404');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Cette page n\'existe pas ou a été déplacé.';

        $this->assertResponseStatusCodeSame(404);
        $this->assertPageTitleSame('Erreur 404');
        $this->assertSelectorTextContains('h1', 'Erreur 404');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Test erreur 404 en Ajax
     * @return void
     */
    public function testNotFoundAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/error-404/ajax');

        $response = $client->getResponse()->getContent();
        $expected = [
            'status' => 'ERROR',
            'data' => [
                'code' => 404,
                'message' => 'Cette page n\'existe pas ou a été déplacé.',
            ],
        ];

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals($expected, json_decode($response, true));
    }

    /**
     * Test erreur 500
     * @return void
     */
    public function testDefault() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/testing/error-500');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Une erreur s\'est produite.';

        $this->assertResponseStatusCodeSame(500);
        $this->assertPageTitleSame('Erreur 500');
        $this->assertSelectorTextContains('h1', 'Erreur 500');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Test erreur 500 en Ajax
     * @return void
     */
    public function testDefaultAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/error-500/ajax');

        $response = $client->getResponse()->getContent();
        $expected = [
            'status' => 'ERROR',
            'data' => [
                'code' => 500,
                'message' => 'Une erreur s\'est produite.',
            ],
        ];

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals($expected, json_decode($response, true));
    }

    /**
     * Test d'erreur inconnu
     * @return void
     */
    public function testUnknown() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/testing/error-0');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Une erreur s\'est produite.';

        $this->assertResponseStatusCodeSame(500);
        $this->assertPageTitleSame('Erreur 500');
        $this->assertSelectorTextContains('h1', 'Erreur 500');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Test d'erreur inconnue en Ajax
     * @return void
     */
    public function testUnknownAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/error-666/ajax');

        $response = $client->getResponse()->getContent();
        $expected = [
            'status' => 'ERROR',
            'data' => [
                'code' => 500,
                'message' => 'Une erreur s\'est produite.',
            ],
        ];

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals($expected, json_decode($response, true));
    }

    /**
     * Test d'un appel ne déclenchant pas d'erreur
     * @return void
     */
    public function testWithoutError() : void
    {
        $client = $this->getHttpClient();
        $this->getHttpClient()->request('GET', '/');

        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringNotContainsStringIgnoringCase('error', $responseContent);
    }

    /**
     * Test d'un appel ne déclenchant pas d'erreur en Ajax
     * @return void
     */
    public function testWithoutErrorAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/ajax');

        $response = $client->getResponse()->getContent();
        $expected = [
            'status' => 'SUCCESS',
            'data' => [
                'success' => true,
            ],
        ];

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($expected, json_decode($response, true));
    }
}