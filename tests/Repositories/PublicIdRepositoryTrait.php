<?php

/**
 * Trait pour les tests des repository gérant des entités avec des identifiants publics
 */

namespace App\Tests\Repositories;

trait PublicIdRepositoryTrait {

    /**
     * Provider pour la recherche par identifiant public
     * @return array
     */
    abstract public function publidIdsProvider() : array;

    /**
     * Test de récupération d'une entité par son identifiant public
     * @param string $publicId
     * @param bool $mustFound Vrai si la recherche par identifiant public doit fonctionner
     * @dataProvider publidIdsProvider
     * @return void
     */
    abstract public function testRetrieveByPublicId(string $publicId, bool $mustFound) : void;

}