<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
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
     * Requête de recherche
     * @param ?string $searching
     * @param int $limit
     * @param int $offset
     */
    public function findBySearching(?string $searching = null, int $limit = 20, int $offset = 0)
    {
        $dql = strtr('SELECT cities FROM :object cities', [
            ':object' => City::class,
        ]);

        if($searching !== null)
        {
            $dql .= ' WHERE cities.name LIKE :name';
        }

        $dql .= ' ORDER BY cities.name ASC';

        $query = $this->getEntityManager()->createQuery($dql);

        if($searching !== null)
        {
            $query->setParameter('name', '%' . $searching . '%');
        }

        

        return $query
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult()
        ;
    }

}
