<?php

/**
 * Trait pour les fixtures chargeant leur données depuis un fichier CSV
 */

namespace App\DataFixtures;

trait WithDataFromCSV {

    /**
     * Données à créer issue d'un fichier CSV
     * @var array
     */
    private array $dataFromCSV = [];

    /**
     * Retourne les données à créer
     * @return void
     */
    abstract private function initDataFromCSV() : void;

    /**
     * Retourne les données du CSV
     * @return array
     */
    public function getDataFromCSV() : array
    {
        return $this->dataFromCSV;
    }
}