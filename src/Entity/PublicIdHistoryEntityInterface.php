<?php

/**
 * Interface pour les entités gérant l'historiques des identifiants publics
 */

namespace App\Entity;

interface PublicIdHistoryEntityInterface {
    
    /**
     * Modifie l'identifiant public
     * @param string $publicId
     * @return self
     */
    public function setPublicId(string $publicId) : self;

}