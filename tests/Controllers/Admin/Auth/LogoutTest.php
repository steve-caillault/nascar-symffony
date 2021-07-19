<?php

/**
 * Tests de la déconnexion au panneau d'administration
 */

namespace App\Tests\Controllers\Admin\Auth;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
/***/
use App\Tests\BaseTestCase;
use App\Tests\WithUserCreating;

final class LogoutTest extends BaseTestCase {

    use WithUserCreating;

    /**
     * Test sans utilisateur connecté
     * @return void
     */
    public function testLogoutWithoutUser() : void
    {
        $client = $this->getHttpClient();
        $client->followRedirects();
        $client->request('GET', '/admin/auth/logout');

        $sessionData = $this->getService(SessionInterface::class)->get('_security_admin');
        $this->assertResponseStatusCodeSame(200);
        $this->assertNull($sessionData);
    }

    /**
     * Test pour un utlisateur avec le ROLE_ADMIN
     * @return void
     */
    public function testLogoutForRoleAdmin() : void
    {
        $user = $this->userToLogged();

        $client = $this->getHttpClient();
        $client->followRedirects();
        $client->loginUser($user, 'admin');
        $client->request('GET', '/admin/auth/logout');

        $sessionData = $this->getService(SessionInterface::class)->get('_security_admin');
        $this->assertResponseStatusCodeSame(200);
        $this->assertNull($sessionData);
    }

    /**
     * Test pour un utilisateur sans le ROLE_ADMIN
     * @return void
     */
    public function testLogoutForRoleUser() : void
    {
        $user = $this->userToLogged('USER');

        $client = $this->getHttpClient();
        $client->followRedirects();
        $client->loginUser($user, 'admin');
        $client->request('GET', '/admin/auth/logout');

        // Seuls les ROLE_ADMIN ont accès à la section /admin
        $sessionData = $this->getService(SessionInterface::class)->get('_security_admin');
        $this->assertResponseStatusCodeSame(403);
        $this->assertNotNull($sessionData);
    }


}