<?php

/**
 * Test de base
 */

namespace App\Tests;

use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use Doctrine\ORM\{
    EntityManagerInterface,
    QueryBuilder
};
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
/***/
use App\Repository\EntityRepositoryInterface;

abstract class BaseTestCase extends WebTestCase {

    use WithFakerTrait;

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
     * Exécute les fixtures dont les noms des classes sont en paramètre
     * @param array $classes
     * @return void
     */
    protected function executeFixtures(array $names) : void
    {
        $this->getService(DatabaseToolCollection::class)->get()->loadFixtures($names);
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

        $this->cleanDatabase();
    }

    /**
     * Nettoyage de la base de données
     * @return void
     */
    private function cleanDatabase() : void
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        $connection = $purger->getObjectManager()->getConnection();

        $isMySQL = ($connection->getDriver() instanceof AbstractMySQLDriver);
        if($isMySQL)
        {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        $purger->purge();

        if($isMySQL)
        {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

}