<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\PilotPublicIdHistory;

/**
 * @method PilotPublicId|null find($id, $lockMode = null, $lockVersion = null)
 * @method PilotPublicId|null findOneBy(array $criteria, array $orderBy = null)
 * @method PilotPublicId[]    findAll()
 * @method PilotPublicId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PilotPublicIdHistoryRepository extends AbstractRepository
{
    use PublicIdHistoryRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PilotPublicIdHistory::class);
    }

    /**
     * Retourne le champs de l'entité ou se trouve la référence de l'objet
     * @return string
     */
    private function getTargetFieldName() : string
    {
        return 'pilot';
    }

}
