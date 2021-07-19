<?php

/**
 * Tests pour les appels Ajax génériques
 */

namespace App\Tests\Controllers;

use App\Tests\BaseTestCase;

final class AjaxControllerTest extends BaseTestCase {

    /**
     * Test d'appel en Ajax
     * @return void
     */
    public function testAjax() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/ajax');
       
        $responseContent = $client->getResponse()->getContent();

        $expectedContent = json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'success' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($expectedContent, $responseContent);
    }

    /**
     * Test d'un appel sans Ajax
     * @return void
     */
    public function testWithoutAjax() : void
    {
        $client = $this->getHttpClient();
        $response = $client->request('GET', '/ajax');

        $messageExpected = 'Vous n\'êtes pas autorisé à accéder à cette page.';
        $message = $response->filter('p')->first()->text();

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1', 'Erreur 403');
        $this->assertEquals($messageExpected, $message);
    }


}