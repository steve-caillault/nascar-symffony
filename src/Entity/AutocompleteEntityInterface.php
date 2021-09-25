<?php

/**
 * Interface pour les entités avec autocomplétion
 */

namespace App\Entity;

interface AutocompleteEntityInterface {

    /**
     * Retourne le texte à afficher dans un champs de formulaire
     * @return string
     */
    public function getAutocompleteDisplayValue() : string;

    /**
     * Retourne l'identifiant
     * @return int|string
     */
    public function getAutocompleteId() : int|string;
    
}