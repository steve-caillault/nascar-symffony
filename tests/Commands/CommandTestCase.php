<?php

/**
 * Classe de base pour les tests d'une commande
 */

namespace App\Tests\Commands;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Tests\BaseTestCase;

abstract class CommandTestCase extends BaseTestCase {

    /**
     * Exécute une commande de la console
     * @param string $name Nom de la commande
     * @param array $parameters Paramètres de la commande
     * @return CommandTester
     */
    protected function executeCommand(string $name, array $parameters = []) : CommandTester
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find($name);
        $commandTester = new CommandTester($command);
        $commandTester->execute($parameters);

        return $commandTester;
    }

    /**
     * Vérification de l'erreur de la commande
     * @param CommandTester $commandTester
     * @param string $errorMessageExpected
     */
    protected function errorCommandChecking(CommandTester $commandTester, string $errorMessageExpected) : void
    {
        // Vérification du statut
        $this->assertEquals(1, $commandTester->getStatusCode());

        // Vérifie le message
        $this->assertStringContainsString($errorMessageExpected, $commandTester->getDisplay());
    }

    /**
     * Vérification après l'appel d'une commande avec un paramètre manquant
     * @param string $missingParameter Le paramètre manquant
     * @param string $responseMessage La réponse de la commande
     * @return void
     */
    protected function missingCommandParameterChecking(
        string $missingParameter, 
        string $responseMessage
    ) : void
    {
        $expectedMessage = strtr('Not enough arguments (missing: ":missing").', [
            ':missing' => $missingParameter,
        ]);
        $this->assertEquals($expectedMessage, $responseMessage);
    }

}