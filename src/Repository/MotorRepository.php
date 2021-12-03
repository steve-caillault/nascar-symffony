<?php

/**
 * Repository pour les moteurs
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
/***/
use App\Entity\{
    Motor,
    MotorPublicIdHistory
};

/**
 * @method Motor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motor[]    findAll()
 * @method Motor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MotorRepository extends AbstractRepository implements SearchingRepositoryInterface
{
    use PublicIdRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motor::class);
    }

     /**
     * Retourne le query builder pour la recherche d'un modèle de voiture
     * @param ?string $searching
     * @return QueryBuilder
     */
    private function getQueryBuilderForSearching(?string $searching) : QueryBuilder
    {
        $query = $this->createQueryBuilder('motors', 'motors.id')
            ->orderBy('motors.name', 'ASC')
        ;

        if($searching !== null)
        {
            $query
                ->where('motors.name LIKE :name')
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
     * @return mixed
     */
    public function findBySearching(?string $searching = null, int $limit = 20, int $offset = 0)
    {
        return $this->getQueryBuilderForSearching($searching)
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
            ->select('COUNT(motors.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Retourne le nom de la classe de l'entité de l'historique à utiliser
     * @return string
     */
    private function getPublicIdHistoryEntityClassName() : string
    {
        return MotorPublicIdHistory::class;
    }

}
