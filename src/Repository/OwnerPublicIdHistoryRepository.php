<?php

/**
 * Repository pour l'historique des identifiants publics des propriétaires
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\OwnerPublicIdHistory;

/**
 * @method OwnerPublicIdHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method OwnerPublicIdHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method OwnerPublicIdHistory[]    findAll()
 * @method OwnerPublicIdHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class OwnerPublicIdHistoryRepository extends AbstractRepository
{
    use PublicIdHistoryRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OwnerPublicIdHistory::class);
    }

     /**
     * Retourne le champs de l'entité ou se trouve la référence de l'objet
     * @return string
     */
    public function getTargetFieldName() : string
    {
        return 'owner';
    }
}
