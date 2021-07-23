<?php

/**
 * Test de la commande de la maintenance
 * php ./vendor/bin/phpunit tests/Command/MaintenanceCommandTest.php
 */

namespace App\Tests\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
/***/
use App\Tests\WithMaintenanceTrait;

final class MaintenanceCommandTest extends CommandTestCase
{
    use WithMaintenanceTrait;

    /**
     * Setup
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->disableMaintenance();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->disableMaintenance();
        parent::tearDown();
    }

    // PARAMETRE activeMaintenance MANQUANT

    /**
     * Test du paramètre activeMaintenance manquant lorsque la maintenance est désactivée
     * @return void
     */
    public function testWithActiveMaintenanceParameterMissingMaintenanceDisabled() : void
    {
        $this->maintenanceWithMissingParameter('activeMaintenance', false);
    }

    /**
     * Test du paramètre activeMaintenance manquant lorsque la maintenance est activée
     * @return void
     */
    public function testWithActiveMaintenanceParameterMissingMaintenanceEnabled() : void
    {
        $this->enableMaintenance();
        $this->maintenanceWithMissingParameter('activeMaintenance', true);
    }

    /************************************************************/

    // PARAMETRE activeMaintenance VIDE

    /**
     * Test du paramètre activeMaintenance vide lorsque la maintenance est désactivée
     * @return void
     */
    public function testWithActiveMaintenanceParameterEmptyMaintenanceDisabled() : void
    {
        $commandTester = $this->getMaintenanceCommandTester('');
        $this->errorCommandChecking($commandTester, 'Le paramètre activeMaintenance ne doit pas être vide.');
        $this->checkingMaintenanceStatus(false);
    }

    /**
     * Test du paramètre activeMaintenance vide lorsque la maintenance est activée
     * @return void
     */
    public function testWithActiveMaintenanceParameterEmptyMaintenanceEnabled() : void
    {
        $this->enableMaintenance();

        $commandTester = $this->getMaintenanceCommandTester('');
        $this->errorCommandChecking($commandTester, 'Le paramètre activeMaintenance ne doit pas être vide.');
        $this->checkingMaintenanceStatus(true);
    }

    /************************************************************/

    // Paramètre activeMaintenance incorrect

    /**
     * Test du paramètre activeMaintenance incorrect lorsque la maintenance est désactivée
     * @return void
     */
    public function testWithActiveMaintenanceParameterIncorrectMaintenanceDisabled() : void
    {
        $activeMaintenance = $this->getFaker()->randomAscii();
        $commandTester = $this->getMaintenanceCommandTester($activeMaintenance);
        $this->errorCommandChecking($commandTester, 'Le paramètre activeMaintenance doit être true ou false.');
        $this->checkingMaintenanceStatus(false);
    }

    /**
     * Test du paramètre activeMaintenance vide lorsque la maintenance est activée
     * @return void
     */
    public function testWithActiveMaintenanceParameterIncorrectMaintenanceEnabled() : void
    {
        $this->enableMaintenance();

        $activeMaintenance = $this->getFaker()->randomAscii();
        $commandTester = $this->getMaintenanceCommandTester($activeMaintenance);
        $this->errorCommandChecking($commandTester, 'Le paramètre activeMaintenance doit être true ou false.');
        $this->checkingMaintenanceStatus(true);
    }

    /************************************************************/

    // TEST DE MISE EN MAINTENANCE

    /**
     * Test ma mise en maintenance lorsqu'elle n'est pas activée
     * @return void
     */
    public function testEnableIfDisabled() : void
    {
        $commandTester = $this->getMaintenanceCommandTester('true');
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertEquals('La maintenance a été activée.' . PHP_EOL, $commandTester->getDisplay());

        $this->checkingMaintenanceStatus(true);
    }

     /**
      * Test la mise en maintenance lorsqu'elle est activée
      * @return void
      */
    public function testEnabledIfEnabled() : void
    {
        $this->enableMaintenance();

        $commandTester = $this->getMaintenanceCommandTester('true');
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertEquals('La maintenance est déjà activée.' . PHP_EOL, $commandTester->getDisplay());

        $this->checkingMaintenanceStatus(true);
    }

    /************************************************************/

    // TEST DE DESACTIVATION DE LA MAINTENANCE

    /**
     * Test la désactivation de la maintenance lorsqu'elle est activée
     * @return void
     */
    public function testDisabledIfEnabled() : void
    {
        $this->enableMaintenance();

        $commandTester = $this->getMaintenanceCommandTester('false');
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertEquals('La maintenance a été désactivée.' . PHP_EOL, $commandTester->getDisplay());

        $this->checkingMaintenanceStatus(false);
    }

     /**
      * Test la désactivation de la maintenance lorsqu'elle n'est pas activée
      * @return void
      */
    public function testDisabledIfDisabled() : void
    {
        $commandTester = $this->getMaintenanceCommandTester('false');
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertEquals('La maintenance est déjà désactivée.' . PHP_EOL, $commandTester->getDisplay());

        $this->checkingMaintenanceStatus(false);
    }

    /************************************************************/

    /**
     * Test de maintenance avec un paramètre manquant
     * @param string $missingParameter Nom du paramètre qui doit être manquant
     * @param bool $maintenanceEnabled Vrai si la maintenance est active
     * @return void 
     */
    private function maintenanceWithMissingParameter(string $missingParameter, bool $maintenanceEnabled) : void
    {
        // Tentative de création
        $message = null;
        try {
            $parameterFilter = function(&$parameters) use ($missingParameter) {
                unset($parameters[$missingParameter]);
            };
            $commandTester = $this->getMaintenanceCommandTester(parameterFilter: $parameterFilter);
        } catch(\Exception $e) {
            $message = $e->getMessage();
        }

        $this->missingCommandParameterChecking($missingParameter, $message);

        // Vérifie l'état de la maintenance
        $this->checkingMaintenanceStatus($maintenanceEnabled);
    }

    /**
     * Vérifie l'état de la maintenance
     * @param bool $maintenanceEnabled Vrai si la maintenance est active
     * @return void
     */
    private function checkingMaintenanceStatus(bool $maintenanceEnabled) : void
    {
        $filePath = $this->getService(ContainerBagInterface::class)->get('maintenance_file_path');
        $methodAssertExists = ($maintenanceEnabled) ? 'assertTrue' : 'assertFalse';
        $this->{ $methodAssertExists }(file_exists($filePath));
    }

    /**
     * Exécute la commande de création maintenance avec l'activation en paramètre
     * @param ?string $activeMaintenance true|false|null
     * @param ?callable $parameterFilter Fonction à appliquer sur les paramètres
     * @return CommandTester
     */
    private function getMaintenanceCommandTester(?string $activeMaintenance = null, ?callable $parameterFilter = null) : CommandTester
    {
        $parameters = [
            'activeMaintenance' => $activeMaintenance,
        ];

        if(! empty($parameterFilter))
        {
            $parameterFilter($parameters);
        }

        return $this->executeCommand('maintenance', $parameters);
    }

}