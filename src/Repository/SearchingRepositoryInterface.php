<?php

/**
 * Interface pour les repository avec recherche
 */

namespace App\Repository;

interface SearchingRepositoryInterface {

    /**
     * Requête de recherche
     * @param ?string $searching
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function findBySearching(?string $searching = null, int $limit = 20, int $offset = 0);

    /**
     * Compte le nombre d'élément d'une recherche
     * @param ?string $searching
     * @return int
     */
    public function getTotalBySearching(?string $searching) : int;

}