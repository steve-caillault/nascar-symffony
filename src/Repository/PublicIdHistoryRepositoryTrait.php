<?php

/**
 * Trait pour les historiques des identifiants publics
 */

namespace App\Repository;

use App\Entity\PublicIdEntityInterface as Entity;

trait PublicIdHistoryRepositoryTrait {

    /**
     * Retourne le champs de l'entité ou se trouve la référence de l'objet
     * @return string
     */
    abstract private function getTargetFieldName() : string;

    /**
     * Vérifie si l'identifiant public existe pour l'entité
     * @param Entity $entity
     * @param string $publicId
     * @return bool
     */
    public function exists(Entity $entity, string $publicId) : bool
    {
        $entityHistoryClass = $this->getClassMetadata()->getName();

        $targetFieldName = $this->getTargetFieldName();


        $dql = strtr('SELECT COUNT(t.public_id) FROM :object t WHERE t.:field = :target AND t.public_id = :public_id', [
            ':object' => $entityHistoryClass,
            ':field' => $targetFieldName,
        ]);

        $count = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters([
                'target' => $entity,
                'public_id' => $publicId
            ])
            ->getSingleScalarResult();

        return ($count > 0);
    }

}