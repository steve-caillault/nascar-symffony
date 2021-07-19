<?php

/**
 * Tests de l'authentification au panneau d'administration depuis un appel Ajax
 */

namespace App\Tests\Controllers\Admin\Auth\Login;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DomCrawler\Crawler;
/***/
use App\Tests\BaseTestCase;
use App\Tests\WithUserCreating;

final class LoginAjaxTest extends BaseTestCase implements LoginAttemptInterface {

    use WithUserCreating;

    /**
     * Retourne l'URL d'authentification à utiliser
     * @return string
     */
    public function getAuthLoginUri() : string
    {
        return $this->getService(RouterInterface::class)->generate('app_admin_security_ajax_login');
    }

    /**
     * Tentative de connexion
     * @param array $credentials
     * @param int $expectedStatus
     * @return Crawler
     */
    public function authAttempt(array $credentials, int $expectedStatus = 200) : Crawler
    {
        $loginUri = $this->getAuthLoginUri();

        $client = $this->getHttpClient();

        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');

        $crawler = $client->xmlHttpRequest('POST', $loginUri, content: json_encode($credentials));

        $this->assertResponseStatusCodeSame($expectedStatus);

        return $crawler;
    }

    /**
     * Vérifie que les identifiants en paramètres sont incorrects
     * @param array $credentials
     * @return void
     */
    public function checkingInvalidCredentials(array $credentials) : void
    {
        // Tentative de connexion
        $this->authAttempt($credentials, 401);

        $expectedContent = json_encode([
            'status' => 'ERROR',
            'data' => [
                'error' => 'Les identifiants sont incorrects.',
            ],
        ]);

        $responseContent = $this->getHttpClient()->getResponse()->getContent();

        $this->assertEquals($expectedContent, $responseContent);
    }
    
    /**
     * Test d'un utilisateur déjà connecté
     * @return void
     */
    public function testAlreadyConnected() : void
    {
        $user = $this->userToLogged();

        $client = $this->getHttpClient();
        
        $client->loginUser($user, 'admin');
        $client->xmlHttpRequest('POST', $this->getAuthLoginUri());
        
        $responseExpected = json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'logged' => true,
            ],
        ]);
        $responseContent = $client->getResponse()->getContent();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($responseExpected, $responseContent);
    }

    /**
     * Test si l'identifiant et le mot de passe sont corrects
     * @return void
     */
    public function testGrantedCredentials() : void
    {
        $user = $this->userToLogged();

        $credentials = [
            'id' => $user->getPublicId(),
            'password' => $user->getTestPassword(),
        ];

        $this->authAttempt($credentials);

        $expectedContent = json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'logged' => true,
            ],
        ]);

        $responseContent = $this->getHttpClient()->getResponse()->getContent();

        $this->assertEquals($expectedContent, $responseContent);
    }

    /**
     * Test d'un appel restreint lorsquil n'y a pas d'utilisateur connecté
     * @return void
     */
    public function testNotAlreadyConnected() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/admin/ajax');

        $loginUrl = $this->getService(RouterInterface::class)->generate(
            'app_admin_security_auth_login', 
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
        );

        $expectedContent = json_encode([
            'status' => 'ERROR',
            'data' => [
                'login_url' => $loginUrl,
            ],
        ]);
        $responseContent = $this->getHttpClient()->getResponse()->getContent();
        
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals($expectedContent, $responseContent);
    }

    /**
     * Test si l'identifiant est incorrect
     * @return void
     */
    public function testIncorrectIdentifier() : void
    {
        $userExpected = $this->userToLogged();

        $credentials = [
            'id' => $this->getFaker()->slug(),
            'password' => $userExpected->getTestPassword(),
        ];

        $this->checkingInvalidCredentials($credentials);
    }

    /**
     * Test si le mot de passe est incorrect
     * @return void
     */
    public function testIncorrectPassword() : void
    {
        $userExpected = $this->userToLogged();

        $credentials = [
            'id' => $userExpected->getPublicId(),
            'password' => $this->getFaker()->password(),
        ];

        $this->checkingInvalidCredentials($credentials);
    }

    /**
     * Test si l'identifiant et le mot de passe sont incorrect
     * @return void
     */
    public function testIncorrectCredentials() : void
    {
        $faker = $this->getFaker();
        $this->userToLogged();

        $credentials = [
            'id' => $faker->slug(),
            'password' => $faker->password(),
        ];

        $this->checkingInvalidCredentials($credentials);
    }

}