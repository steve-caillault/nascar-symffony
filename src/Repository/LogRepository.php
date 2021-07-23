<?php

/**
 * Repository pour les logs en base de données
 */

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\Log;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LogRepository extends AbstractRepository 
{
    /**
     * @inheritdoc
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

}
