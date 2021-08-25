<?php

/**
 * Service pour la gestion de l'image d'un états
 */

namespace App\Service\State;

final class CountryStateFlagService extends AbstractFlagService {

    /**
     * Retourne le répertoire où sont stockées les images
     * @return string
     */
    protected function getDirectory() : string
    {
       return 'images/states/'; 
    }

}