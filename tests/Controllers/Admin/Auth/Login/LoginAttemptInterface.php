<?php

/**
 * Trait pour tester l'authentification
 */

namespace App\Tests\Controllers\Admin\Auth\Login;

use Symfony\Component\DomCrawler\Crawler;

interface LoginAttemptInterface {

    /**
     * Retourne l'URL d'authentification à utiliser
     * @return string
     */
    public function getAuthLoginUri() : string;

    /**
     * Tentative de connexion
     * @param array $credentials
     * @param int $expectedStatus
     * @return Crawler
     */
    public function authAttempt(array $credentials, int $expectedStatus = 200) : Crawler;

    /**
     * Vérifie que les identifiants en paramètres sont incorrects
     * @param array $credentials
     * @return void
     */
    public function checkingInvalidCredentials(array $credentials) : void;

}