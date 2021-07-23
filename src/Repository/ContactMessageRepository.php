<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\ContactMessage;

/**
 * @method ContactMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactMessage[]    findAll()
 * @method MessContactMessageage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ContactMessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    /**
     * Compte le nombre de messages
     * @return int
     */
    public function getTotal() : int
    {
        $dql = strtr('SELECT COUNT(messages.id) FROM :object messages', [
            ':object' => ContactMessage::class,
        ]);

        $total = (int) $this->getEntityManager()->createQuery($dql)->getSingleScalarResult();
        return $total;
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
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
    public function findOneBySomeField($value): ?Message
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
