<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
/***/
use App\Entity\City;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CityRepository extends AbstractRepository implements SearchingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * Retourne une ville à partir de son nom
     * @param string $name
     * @return ?City
     */
    public function findByName(string $name) : ?City
    {
        return $this->findOneBy([
            'name' => $name,
        ]);
    }

    /**
     * Retourne le query builder pour la recherche d'un pilote
     * @param ?string $searching
     * @return QueryBuilder
     */
    private function getQueryBuilderForSearching(?string $searching) : QueryBuilder
    {
        $query = $this->createQueryBuilder('cities', 'cities.id')
            ->orderBy('cities.name', 'ASC')
        ;

        if($searching !== null)
        {
            $query
                ->where('cities.name LIKE :name')
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
            ->select('COUNT(cities.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

}
