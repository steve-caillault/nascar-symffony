<?php

/**
 * Trait pour l'activation et la désactivation de la maintenance dans les tests
 */

namespace App\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

trait WithMaintenanceTrait {

    /**
     * Active la maintenance
     * @return void
     */
    private function enableMaintenance() : void
    {
        $filePath = $this->getService(ContainerBagInterface::class)->get('maintenance_file_path');
        if(! file_exists($filePath))
        {
            (new Filesystem())->dumpFile($filePath, '');
        }
    }

    /**
     * Désactive la maintenance
     * @return void
     */
    private function disableMaintenance() : void
    {
        $filePath = $this->getService(ContainerBagInterface::class)->get('maintenance_file_path');
        
        if(file_exists($filePath))
        {
            unlink($filePath);
        }
    }

}