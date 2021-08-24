<?php

/**
 * Service pour la gestion de l'image d'un pays
 */

namespace App\Service\State;

final class CountryFlagService extends AbstractFlagService {

    /**
     * Retourne le répertoire où sont stockées les images
     * @return string
     */
    protected function getDirectory() : string
    {
       return 'images/countries/'; 
    }

}