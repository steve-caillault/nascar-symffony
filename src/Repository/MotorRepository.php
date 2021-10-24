<?php

/**
 * Repository pour les moteurs
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\Motor;

/**
 * @method Motor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motor[]    findAll()
 * @method Motor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotorRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motor::class);
    }

    // /**
    //  * @return Motor[] Returns an array of Motor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Motor
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
