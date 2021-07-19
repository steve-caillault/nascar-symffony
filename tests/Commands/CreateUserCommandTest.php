<?php

/**
 * Test de la commande de la création d'utilisateur
 * bin/phpunit tests/Command/CreateUserCommandTest.php
 */

namespace App\Tests\Commands;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Tester\CommandTester;
/***/
use App\Entity\User;
use App\Tests\Commands\CommandTestCase;
use App\Tests\WithUserGenerating;

final class CreateUserCommandTest extends CommandTestCase
{
    use WithUserGenerating;

    // SUCCES DE LA CREATION

    /**
     * Création d'un administrateur
     * @return void
     */
    public function testCreateAdmin() : void
    {
        $this->createUserChecking(User::PERMISSION_ADMIN);
    }

    /*************************************************************************/

    // TEST DE L'IDENTIFIANT

    /**
     * Tentative de création sans identifiant public
     * @return void
     */
    public function testCreateUserWithoutIdentifier() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setPublicId('');

        $this->createUserWithMissingParameter($expectedUser, 'publicId');
    }

    /**
     * Tentative de création avec un identifiant vide
     * @return void
     */
    public function testCreatedUserWithEmptyIdentifier() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setPublicId('');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'L\'identifiant de l\'utilisateur est nécessaire.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de connexion avec un identifiant trop court
     *  @return void
     */
    public function testCreatedUserWithIdentifierTooShort() : void
    {
        $identifier = substr($this->getFaker()->slug(1), 0, 1);

        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setPublicId($identifier);

        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'L\'identifiant doit avoir au moins 3 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de connexion avec un identifiant trop long
     *  @return void
     */
    public function testCreatedUserWithIdentifierTooLong() : void
    {
        $identifier = substr($this->getFaker()->slug(500), 0, 150);

        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setPublicId($identifier);

        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'L\'identifiant ne doit pas avoir plus de 50 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de création avec un identifiant déjà existant
     * @return void
     */
    public function testCreatedUserAlreadyExists() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        
        $generatorUser->next();

        $expectedUser = $generatorUser->current();

        // Enregistrement de l'utilisateur généré en base de données
        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($expectedUser);
            $entityManager->flush();
        } catch(\Exception $e) {

        }

        // Tentative de création
        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = strtr('L\'utilisateur :name existe déjà.', [
            ':name' => $expectedUser->getPublicId(),
        ]);
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage, true);
    }

    /*************************************************************************/

    // TEST  DU PRENOM

    /**
     * Tentative de création avec le prénom manquant
     * @return void
     */
    public function testCreateUserWithMissingFirstName() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setFirstName('');

        $this->createUserWithMissingParameter($expectedUser, 'firstName');
    }

    /**
     * Tentative de création avec le prénom vide
     * @return void
     */
    public function testCreateUserWithEmptyFirstName() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setFirstName('');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le prénom de l\'utilisateur est nécessaire.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de création avec un prénom trop court
     * @return void
     */
    public function testCreateUserWithFirstNameTooShort() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();

        $expectedUser = $generatorUser->current();
        $expectedUser->setFirstName('f');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       

        $errorMessage = 'Le prénom doit avoir au moins 3 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de connexion avec un prénom trop long
     *  @return void
     */
    public function testCreatedUserWithFirstNameTooLong() : void
    {
        $firstName = substr($this->getFaker()->text(500), 0, 150);

        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setFirstName($firstName);

        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'Le prénom ne doit pas avoir plus de 100 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /*************************************************************************/

    /**
     * Tentative de création avec le nom manquant
     * @return void
     */
    public function testCreateUserWithMissingLastName() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setLastName('');

        $this->createUserWithMissingParameter($expectedUser, 'lastName');
    }

    /**
     * Tentative de création avec le nom vide
     * @return void
     */
    public function testCreateUserWithEmptyLastName() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setLastName('');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le nom de l\'utilisateur est nécessaire.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de création avec un nom trop court
     * @return void
     */
    public function testCreateUserWithLastNameTooShort() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();
        
        $expectedUser->setLastName('u');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le nom doit avoir au moins 3 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de connexion avec un nom trop long
     * @return void
     */
    public function testCreatedUserWithLastNameTooLong() : void
    {
        $name = substr($this->getFaker()->slug(500), 0, 150);

        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setLastName($name);

        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'Le nom ne doit pas avoir plus de 100 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /*************************************************************************/

    // TEST DE LA PERMISSION

    /**
     * Tentative de création avec une permission incorrecte
     * @return void
     */
    public function testCreateUserWithForbiddenPermission() : void
    {
        $generatorUser = $this->getGeneratingUser('WRITER');
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        // Tentative de création
        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $errorMessage = 'Les permissions de l\'utilisateur sont incorrectes.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de création sans permission
     * @return void
     */
    public function testCreateUserWithoutPermission() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $this->createUserWithMissingParameter($expectedUser, 'role');
    }

    /**
     * Tentative de création avec une permission vide
     * @return void
     */
    public function testCreateUserWithEmptyPermission() : void
    {
        $generatorUser = $this->getGeneratingUser('');
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        // Tentative de création
        $commandTester = $this->getCreateUserCommandTester($expectedUser);

        $this->createUserErrorChecking($expectedUser, $commandTester, 'Les permissions sont nécessaires.');
    }

    /*************************************************************************/

    // TEST DU MOT DE PASSE

     /**
     * Tentative de création avec le mot de passe manquant
     * @return void
     */
    public function testCreateUserWithMissingPassword() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setTestPassword('');

        $this->createUserWithMissingParameter($expectedUser, 'password');
    }

    /**
     * Tentative de création avec un mot de passe vide
     * @return void
     */
    public function testCreateUserWithEmptyPassword() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setTestPassword('');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le mot de passe doit avoir entre 3 et 50 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /**
     * Tentative de création avec un mot de passe trop court
     * @return void
     */
    public function testCreateUserWithPasswordTooShort() : void
    {
        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setTestPassword('xr');

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le mot de passe doit avoir entre 3 et 50 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

     /**
     * Tentative de création avec un mot de passe trop long
     * @return void
     */
    public function testCreateUserWithPasswordTooLong() : void
    {
        $password = $this->getFaker()->text();

        $generatorUser = $this->getGeneratingUser(User::PERMISSION_ADMIN);
        $generatorUser->next();
        $expectedUser = $generatorUser->current();

        $expectedUser->setTestPassword($password);

        $commandTester = $this->getCreateUserCommandTester($expectedUser);
       
        $errorMessage = 'Le mot de passe doit avoir entre 3 et 50 caractères.';
        $this->createUserErrorChecking($expectedUser, $commandTester, $errorMessage);
    }

    /*************************************************************************/

    /**
     * Exécute la commande de création d'un utilisateur dont l'entité désirée est en paramètre
     * @param User $expectedUser
     * @param ?callable $parameterFilter Fonction à appliquer sur les paramètres
     * @return CommandTester
     */
    private function getCreateUserCommandTester(User $expectedUser, ?callable $parameterFilter = null) : CommandTester
    {
        $parameters = [
            'publicId' => $expectedUser->getPublicId(),
            'firstName' => $expectedUser->getFirstName(),
            'lastName' => $expectedUser->getLastName(),
            'password' => $expectedUser->getTestPassword(),
            'role' => current($expectedUser->getPermissions()) ?: '',
        ];

        if(! empty($parameterFilter))
        {
            $parameterFilter($parameters);
        }

        // Création d'un utilisateur
        return $this->executeCommand('create-user', $parameters);
    }

    /**
     * Test de création avec un paramètre manquant
     * @param User $expectedUser
     * @param string $missingParameter Nom du paramètre qui doit être manquant
     * @return void 
     */
    private function createUserWithMissingParameter(User $expectedUser, string $missingParameter) : void
    {
        $expectedUserPublicId = $expectedUser->getPublicId();

        // Tentative de création
        $message = null;
        try {
            $commandTester = $this->getCreateUserCommandTester($expectedUser, function(&$parameters) use ($missingParameter) {
                unset($parameters[$missingParameter]);
            });
        } catch(\Exception $e) {
            $message = $e->getMessage();
        }
        
        $this->missingCommandParameterChecking($missingParameter, $message);

        // Vérifie que l'utilisateur n'a pas été créé
        $userCreated = $this->getQueryBuilder(User::class)
            ->andWhere('t.public_id = :publicId')
            ->setParameter(':publicId', $expectedUserPublicId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $this->assertEmpty($userCreated);
    }

    /**
     * Vérification de création avec une erreur
     * @param User $expectedUser
     * @param CommandTester $commandTester
     * @param string $errorMessageExpected
     * @param bool $userAlreadyExists Vrai si l'utilisateur existe déjà en base de données
     * @return void
     */
    private function createUserErrorChecking(
        User $expectedUser, 
        CommandTester $commandTester, 
        string $errorMessageExpected,
        bool $userAlreadyExists = false
    )
    {
        $this->errorCommandChecking($commandTester, $errorMessageExpected);

        $expectedUserPublicId = $expectedUser->getPublicId();

        $userCreated = $this->getQueryBuilder(User::class)
            ->andWhere('t.public_id = :publicId')
            ->setParameter(':publicId', $expectedUserPublicId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if($userAlreadyExists)
        {
            $this->assertEquals($expectedUser, $userCreated);
        }
        else
        {
            $this->assertEmpty($userCreated);
        }
    }

    /**
     * Test de création réussie
     * @param string $role
     * @return void
     */
    private function createUserChecking(string $role) : void
    {
        $generatorUser = $this->getGeneratingUser($role);

        $userDataExpected = $generatorUser->current();
        $generatorUser->next();
        $userExpected = $generatorUser->current();

        
        $expectedUserPublicId = $userDataExpected['public_id'];

        // Création d'un utilisateur
        $commandTester = $this->getCreateUserCommandTester($userExpected);

        // Vérification du statut
        $this->assertEquals(0, $commandTester->getStatusCode());

        // Vérifie que l'utilisateur a été créé
        $userCreated = $this->getQueryBuilder(User::class)
            ->andWhere('t.public_id = :publicId')
            ->setParameter(':publicId', $expectedUserPublicId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
            
        $this->assertNotNull($userCreated->getId());
        $this->assertEquals([
            'public_id' => $userDataExpected['public_id'],
            'first_name' => $userDataExpected['first_name'],
            'last_name' => $userDataExpected['last_name'],
            'permissions' => [ $userDataExpected['role'] ],
        ], [
            'public_id' => $userCreated->getPublicId(),
            'first_name' => $userCreated->getFirstName(),
            'last_name' => $userCreated->getLastName(),
            'permissions' => $userCreated->getPermissions(),
        ]);

        // Vérification que les deux mots de passe sont équivalent
        $encoder = $this->getService(UserPasswordHasherInterface::class);
        $isValidPassword = $encoder->isPasswordValid($userCreated, $userDataExpected['test_password']);
        $this->assertTrue($isValidPassword);
    }

    /*************************************************************************/

}
