<?php

/**
 * Création d'un utilisateur
 * php bin/console create-user id firstName lastName password role
 * php bin/console create-user charles-dickens Charles Dickens azerty admin
 */

namespace App\Command;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Input\{ 
    InputInterface, 
    InputArgument 
};
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
/***/
use App\Entity\User;

final class CreateUserCommand extends BaseCommand
{
    /**
     * @inheritdoc
     */
    protected static $defaultName = 'create-user';

    /********************************************************/

    /**
     * Constructeur
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $encoder
    )
    {
        parent::__construct(static::$defaultName);
    }

    /********************************************************/

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Création d\'un utilisateur.')
            ->addArgument('publicId', InputArgument::REQUIRED, 'Identifiant public de l\'utilisateur.')
            ->addArgument('firstName', InputArgument::REQUIRED, 'Prénom de l\'utilisateur.')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Nom de l\'utilisateur.')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'utilisateur.')
            ->addArgument('role', InputArgument::REQUIRED, 'Rôle de l\'utilisateur.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $allowedArguments = [ 'publicId', 'firstName', 'lastName', 'password', 'role' ];
        $arguments = array_filter($input->getArguments(), fn($name) => in_array($name, $allowedArguments), ARRAY_FILTER_USE_KEY);
        list($userId, $firstName, $lastName, $userPassword, $userRole) = array_values($arguments);
 
        // Vérifie que l'utilisateur n'existe pas déjà
        $existingUser = $userRepository->findByPublicId($userId);
        if($existingUser !== null)
        {
            $message = strtr('L\'utilisateur :name existe déjà.', [
                ':name' => $existingUser->getPublicId(),
            ]);
            return $this->exitWithMessage($output, $message, self::FAILURE);
        }

        // Vérifie la longueur du mot de passe 
        $minLengthPassword = 3;
        $maxLengthPassword = 50;
        $passwordLength = strlen($userPassword);
        if($passwordLength < $minLengthPassword or $passwordLength > $maxLengthPassword)
        {
            $message = strtr('Le mot de passe doit avoir entre :min et :max caractères.', [
                ':min' => $minLengthPassword,
                ':max' => $maxLengthPassword,
            ]);
            return $this->exitWithMessage($output, $message, self::FAILURE);
        }

        $user = (new User())
            ->setPublicId($userId)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->addPermission(strtoupper($userRole))
        ;
        
        // Crypte le mot de passe
        $passwordEncoder = $this->encoder->hashPassword($user, $userPassword);
        $user->setPasswordHashed($passwordEncoder);

        // Gestion des erreurs
        $errors = $this->validator->validate($user);
        if($errors->count() > 0)
        {
            $messages = [];
            foreach($errors as $error)
            {
                $messages[] = $error->getMessage();
            }
            $message = implode(' ', $messages);
            return $this->exitWithMessage($output, $message, self::FAILURE);
        }

        // Enregistrement
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch(\Exception $e) {

        }
    
        // Vérification
        $userCreated = ($userRepository->findByPublicId($userId)?->getId() !== null);
        $responseStatus = ($userCreated) ? self::SUCCESS : self::FAILURE;
        if($userCreated)
        {
            $message = strtr('L\'utilisateur :name a été créé.', [
                ':name' => $user->getFullName(),
            ]);
        }
        else
        {
            $message = strtr('L\'utilisateur :name n\'a pa pu être créé.', [
                ':name' => $user->getFullName(),
            ]);
        }

        return $this->exitWithMessage($output, $message, $responseStatus);
    }

    
}
