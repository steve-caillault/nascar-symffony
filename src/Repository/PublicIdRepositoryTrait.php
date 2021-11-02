<?php

/**
 * Trait pour les rtepository des entités ayant un identifiant public
 */

namespace App\Repository;

use Doctrine\ORM\Query\Expr\Join;
/***/
use App\Entity\PublicIdEntityInterface;

trait PublicIdRepositoryTrait {

    /**
     * Retourne le nom de la classe de l'entité de l'historique à utiliser
     * @return string
     */
    abstract private function getPublicIdHistoryEntityClassName() : string;

    /**
     * Retourne une entité à partir de son identifiant public
     * @param string $publicId
     * @return ?PublicIdEntityInterface
     */
    public function findByPublicId(string $publicId) : ?PublicIdEntityInterface
    {
        $table = $this->getClassMetadata()->getTableName();

        $historyClassName = $this->getPublicIdHistoryEntityClassName();
        $targetField = $this->getEntityManager()->getRepository($historyClassName)->getTargetFieldName();

        return $this->createQueryBuilder($table, $table . '.id')
            ->leftJoin($historyClassName, 'public_ids', Join::WITH, 'public_ids.' . $targetField . ' = ' . $table . '.id')
            ->where($table . '.public_id = :publicId')
            ->orWhere('public_ids.public_id = :publicId')
            ->setParameter('publicId', $publicId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}