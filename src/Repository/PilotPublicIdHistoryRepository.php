<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};

/**
 * @method PilotPublicId|null find($id, $lockMode = null, $lockVersion = null)
 * @method PilotPublicId|null findOneBy(array $criteria, array $orderBy = null)
 * @method PilotPublicId[]    findAll()
 * @method PilotPublicId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PilotPublicIdHistoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PilotPublicIdHistory::class);
    }

    /**
     * Vérifie si l'identifiant public existe pour le pilote
     * @param Pilot $pilot
     * @param string $publicId
     * @return bool
     */
    public function exists(Pilot $pilot, string $publicId) : bool
    {
        $dql = strtr('SELECT COUNT(t.public_id) FROM :object t WHERE t.pilot = :pilot AND t.public_id = :public_id', [
            ':object' => PilotPublicIdHistory::class,
        ]);

        $count = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters([
                'pilot' => $pilot,
                'public_id' => $publicId
            ])
            ->getSingleScalarResult();

        return ($count > 0);
    }

    // /**
    //  * @return PilotPublicId[] Returns an array of PilotPublicId objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PilotPublicId
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
