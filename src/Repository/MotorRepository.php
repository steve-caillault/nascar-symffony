<?php

/**
 * Repository pour les moteurs
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
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
final class MotorRepository extends AbstractRepository
{
    use PublicIdRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motor::class);
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
