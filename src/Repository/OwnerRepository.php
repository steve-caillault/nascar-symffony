<?php

/**
 * Repository pour les propriétaires
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\{
    Owner,
    OwnerPublicIdHistory
};

/**
 * @method Owner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Owner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Owner[]    findAll()
 * @method Owner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OwnerRepository extends AbstractRepository
{
    use PublicIdRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Owner::class);
    }

    /**
     * Retourne le nom de la classe de l'entité de l'historique à utiliser
     * @return string
     */
    private function getPublicIdHistoryEntityClassName() : string
    {
        return OwnerPublicIdHistory::class;
    }

}
