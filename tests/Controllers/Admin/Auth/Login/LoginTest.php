<?php

/**
 * Tests de l'authentification au panneau d'administration
 */

namespace App\Tests\Controllers\Admin\Auth\Login;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
/***/
use App\Tests\BaseTestCase;
use App\Tests\WithUserCreating;

final class LoginTest extends BaseTestCase implements LoginAttemptInterface {

    use WithUserCreating;

    /**
     * Retourne l'URL d'authentification à utiliser
     * @return string
     */
    public function getAuthLoginUri() : string
    {
        return $this->getService(RouterInterface::class)->generate('app_admin_security_auth_login');
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
        $client->followRedirects();

        $crawler = $client->request('GET', $loginUri);
        $this->assertResponseStatusCodeSame(200);

        $submitButton = $crawler->filter('form.login-form input[type=submit]');
        $formLogin = $submitButton->form();

        $response = $client->submit($formLogin, $credentials);

        $this->assertResponseStatusCodeSame($expectedStatus);

        return $response;
    }

    /**
     * Vérifie que les identifiants en paramètres sont incorrects
     * @param array $credentials
     * @return void
     */
    public function checkingInvalidCredentials(array $credentials) : void
    {
        // Tentative de connexion
        $responsePost = $this->authAttempt($credentials);
        $error = $responsePost->filter('div.form-input p.error')->getNode(0)?->textContent;
        $this->assertEquals('Les identifiants sont incorrects.', $error);
    }
    
    /**
     * Test qu'un utilisateur n'est pas redirigé vers la page de connection s'il est déjà connecté
     * @return void
     */
    public function testAlreadyConnected() : void
    {
        $user = $this->userToLogged();
        $uriNotExpected = $this->getAuthLoginUri();

        $client = $this->getHttpClient();
        
        $client->followRedirects();
        $client->loginUser($user, 'admin');
        $client->request('GET', '/admin');
        
        $location = $client->getRequest()->getUri();
        $this->assertResponseStatusCodeSame(200);
        $this->assertStringNotContainsString($uriNotExpected, $location);
    }

    /**
     * Test si l'identifiant et le mot de passe sont corrects
     * @return void
     */
    public function testGrantedCredentials() : void
    {
        $user = $this->userToLogged();

        $uriNotExpected = $this->getAuthLoginUri();

        $credentials = [
            'id' => $user->getPublicId(),
            'password' => $user->getTestPassword(),
        ];

        $this->authAttempt($credentials);

        $location = $this->getHttpClient()->getRequest()->getUri();
        $this->assertStringNotContainsString($uriNotExpected, $location);
        $this->assertBrowserNotHasCookie('ADMIN_REMEMBER_ME');
    }

    /**
     * Test de la création du cookie remember me
     * @return void
     */
    public function testGrantedCredentialsWithRememberMe()
    {
        $user = $this->userToLogged();

        $uriNotExpected = $this->getAuthLoginUri();

        $credentials = [
            'id' => $user->getPublicId(),
            'password' => $user->getTestPassword(),
            'remember_me' => 'on',
        ];

        $this->authAttempt($credentials);

        $location = $this->getHttpClient()->getRequest()->getUri();
        $this->assertStringNotContainsString($uriNotExpected, $location);
        $this->assertBrowserHasCookie('ADMIN_REMEMBER_ME');
    }

    /**
     * Test qu'un utilisateur est redirigé vers la page de connection s'il n'est pas connecté
     * @return void
     */
    public function testNotAlreadyConnected() : void
    {
        $uriExpected = $this->getAuthLoginUri();

        $client = $this->getHttpClient();
        $client->followRedirects();
        $client->request('GET', '/admin');

        $location = $client->getRequest()->getUri();
        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString($uriExpected, $location);
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
        $this->userToLogged();

        $faker = $this->getFaker();

        $credentials = [
            'id' => $faker->slug(),
            'password' => $faker->password(),
        ];

        $this->checkingInvalidCredentials($credentials);
    }

}