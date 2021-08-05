<?php

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * Retourne la saison en cours
     * @return ?Season
     */
    public function findCurrent() : ?Season
    {
        $dql = strtr('SELECT seasons FROM :object seasons WHERE seasons.state = :state', [
            ':object' => Season::class,
        ]);
        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('state', Season::STATE_CURRENT)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Compte le nombre de messages
     * @return int
     */
    public function getTotal() : int
    {
        $dql = strtr('SELECT COUNT(seasons.id) FROM :object seasons', [
            ':object' => Season::class,
        ]);

        $total = (int) $this->getEntityManager()->createQuery($dql)->getSingleScalarResult();
        return $total;
    }

    // /**
    //  * @return Season[] Returns an array of Season objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Season
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
