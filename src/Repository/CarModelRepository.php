<?php

/**
 * Repository pour les modèles de voiture
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\CarModel;

/**
 * @method CarModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarModel[]    findAll()
 * @method CarModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CarModelRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarModel::class);
    }

    /**
     * Retourne une liste de modèle de voiture en chargeant les moteurs
     * @param array $orderBy Liste des tris à appliquer
     * @param int $limit
     * @param int $offset
     */
    public function getListWithMotorLoaded(array $orderBy, int $limit, int $offset = 0) : array
    {
        $queryBuilder = $this->createQueryBuilder('cars', 'cars.id')
            ->join('cars.motor', 'motors')
            ->addSelect('motors')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        foreach($orderBy as $sort => $direction)
        {
            $queryBuilder->addOrderBy('cars.' . $sort, $direction);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Compte le nombre de modèles
     * @return int
     */
    public function getTotal() : int
    {
        $dql = sprintf('SELECT COUNT(car_models.id) FROM %s car_models', CarModel::class);

        $total = (int) $this->getEntityManager()->createQuery($dql)->getSingleScalarResult();
        return $total;
    }

    // /**
    //  * @return CarModel[] Returns an array of CarModel objects
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
    public function findOneBySomeField($value): ?CarModel
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
