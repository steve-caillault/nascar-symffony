<?php

/**
 * Trait pour la création d'utilisateur
 */

namespace App\Tests;

use Psr\Log\LoggerInterface;
/***/
use App\Entity\User;

trait WithUserCreating {

    use WithUserGenerating;
    
    /**
     * Retourne un utilisateur à loguer
     * @param string $permission Permission à utiliser
     * @return User
     */
    private function userToLogged(string $permission = User::PERMISSION_ADMIN) : User
    {
        $generatorUser = $this->getGeneratingUser($permission);
        $generatorUser->next();
        $user = $generatorUser->current();

        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch(\Exception $e) {
            $this->getService(LoggerInterface::class)->debug($e->getMessage());
        }

        return $user;
    }

}