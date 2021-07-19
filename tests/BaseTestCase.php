<?php

/**
 * Test de base
 */

namespace App\Tests;

use Doctrine\ORM\{
    EntityManagerInterface,
    QueryBuilder
};
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\{ 
    Factory as FakerFactory, 
    Generator as FakerGenerator 
};
/***/
use App\Repository\EntityRepositoryInterface;

abstract class BaseTestCase extends WebTestCase {

    /**
     * Client HTTP
     * @var KernelBrowser
     */
    private KernelBrowser $httpClient;

    /**
     * Entity Manager
     * @var ?EntityManagerInterface
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Objet Faker pour générer de fausse données
     * @var FakerGenerator|false
     */
    private FakerGenerator|false $faker = false;

    /**
     * Retourne l'objet Faker permettant de générer de fausses données
     * @return FakerGenerator
     */
    protected function getFaker() : FakerGenerator
    {
        if($this->faker === false)
        {
            $this->faker = FakerFactory::create();
        }
        return $this->faker;
    }

    /**
     * Retourne le service correspondant à la classe en paramètre
     * @param string $class
     * @return mixed
     */
    protected function getService(string $class)
    {
        return static::getContainer()->get($class);
    }

    /**
     * Retourne l'entity manager
     * @return ?EntityManagerInterface
     */
    protected function getEntityManager() : ?EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * Retourne le repository de la classe en paramètre
     * @param string $class
     * @return EntityRepositoryInterface
     */
    protected function getRepository(string $class) : EntityRepositoryInterface
    {
        return $this->entityManager->getRepository($class);
    }

    /**
     * Retourne un query builder pour la classe de l'entité en paramètre
     * @param string $entityClass
     * @param string $tableAlias
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $entityClass, string $tableAlias = 't') : QueryBuilder
    {
        return ($this->getRepository($entityClass))->createQueryBuilder($tableAlias);
    }

    /**
     * Retourne le client HTTP
     * @return KernelBrowser
     */
    protected function getHttpClient() : KernelBrowser
    {
        return $this->httpClient;
    }

    /**
     * Setup
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->httpClient = static::createClient();

        $kernel = static::$kernel;

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Pour pouvoir déterminer les requêtes à annuler dans static::tearDown()
        // @todo Trouver une autre solution pour la remise à 0 : créer des problèmes lorsque l'on tente de vérifier
        // que valeur créé dans le test existe.
        // $this->entityManager->getConnection()->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // $this->entityManager->getConnection()->rollback(); // Retour à l'état initial
        $this->entityManager->close();
        $this->entityManager = null;
    }

}