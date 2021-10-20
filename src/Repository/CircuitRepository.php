<?php

/**
 * Repository pour les circuits
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
/***/
use App\Entity\Circuit;

/**
 * @method Circuit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circuit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circuit[]    findAll()
 * @method Circuit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CircuitRepository extends AbstractRepository implements SearchingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circuit::class);
    }

    /**
     * Retourne le query builder pour la recherche d'un pilote
     * @param ?string $searching
     * @return QueryBuilder
     */
    private function getQueryBuilderForSearching(?string $searching) : QueryBuilder
    {
        $query = $this->createQueryBuilder('circuits', 'circuits.id')
            ->orderBy('circuits.name', 'ASC')
        ;

        if($searching !== null)
        {
            $query
                ->where('circuits.name LIKE :name')
                ->setParameter('name', '%' . $searching . '%')   
            ;
        }

        return $query;
    }
    /**
     * Requête de recherche
     * @param ?string $searching
     * @param int $limit
     * @param int $offset
     */
    public function findBySearching(?string $searching = null, int $limit = 20, int $offset = 0)
    {
        $query = $this->getQueryBuilderForSearching($searching);
        
        return $query
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Compte le nombre d'élément d'une recherche
     * @param ?string $searching
     * @return int
     */
    public function getTotalBySearching(?string $searching) : int
    {
        return $this->getQueryBuilderForSearching($searching)
            ->select('COUNT(circuits.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    // /**
    //  * @return Circuit[] Returns an array of Circuit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Circuit
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
