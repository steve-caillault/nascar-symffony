<?php

/**
 * Trait pour les tests ayant besoin de générer des utilisateurs
 */

namespace App\Tests;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/***/
use App\Entity\User;

trait WithUserGenerating {

    /**
     * Génére un utilisateur sans le créer
     * @param string $role
     * @return \Generator
     */
    private function getGeneratingUser(string $role = User::PERMISSION_ADMIN) : \Generator
    {
        $faker = $this->getFaker();

        $data = [
            'public_id' => $faker->slug($faker->numberBetween(1, 3)),
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'test_password' => $faker->password(),
            'role' => $role,
        ];

        $user = (new User())
            ->setPublicId($data['public_id'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setTestPassword($data['test_password'])
            ->addPermission($data['role'])
        ;

        $encoder = $this->getService(UserPasswordHasherInterface::class);
        $passwordHashed = $encoder->hashPassword($user, $data['test_password']);
        $user->setPasswordHashed($passwordHashed);

        yield $data;
        yield $user;
    }

}