<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
/***/
use App\Entity\{
    Pilot,
    PilotPublicIdHistory
};

/**
 * @method Pilot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pilot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pilot[]    findAll()
 * @method Pilot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PilotRepository extends AbstractRepository implements SearchingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pilot::class);
    }

    /**
     * Retourne un pilote à partir de son identifiant public
     * @param string $publicId
     * @return ?Pilot
     */
    public function findByPublicId(string $publicId) : ?Pilot
    {
        return $this->createQueryBuilder('pilots', 'pilots.id')
            ->leftJoin(PilotPublicIdHistory::class, 'public_ids', Join::WITH, 'public_ids.pilot = pilots.id')
            ->where('pilots.public_id = :publicId')
            ->orWhere('public_ids.public_id = :publicId')
            ->setParameter('publicId', $publicId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne le champs du nom complet
     * @param string $tableAlias
     * @return string
     */
    private function getFullNameField(string $tableAlias = 'p') : string
    {
        $fields = [ 'first_name', 'last_name', ];
        $fullNameField = strtr('CONCAT_WS(\' \', :fields)', [
            ':fields' => implode(', ', array_map(fn($field) => $tableAlias . '.' . $field, $fields))
        ]);

        return $fullNameField;
    }

    /**
     * Retourne le query builder pour la recherche d'un pilote
     * @param ?string $searching
     * @return QueryBuilder
     */
    private function getQueryBuilderForSearching(?string $searching) : QueryBuilder
    {
        $query = $this->createQueryBuilder('p', 'p.id');

        $fullNameField = $this->getFullNameField();

        $query->addSelect($fullNameField . ' AS HIDDEN fullname');
        
        if($searching !== null)
        {
            $query->andWhere($query->expr()->orX(
                $query->expr()->like('p.first_name', ':searching'),
                $query->expr()->like('p.last_name', ':searching'),
                $query->expr()->like($fullNameField, ':searching'),
            ));

            $query->setParameter('searching', '%' . $searching . '%');
        }

        return $query;
    }

    /**
     * Retourne un pilote en fonction de son nom complet
     * @param string $fullName
     * @return ?Pilot
     */
    public function findByFullName(string $fullName) : ?Pilot
    {
        $fullNameField = $this->getFullNameField();

        return $this->createQueryBuilder('p', 'p.id')
            ->addSelect($fullNameField . ' AS HIDDEN fullname')
            ->where('fullname = :fullname')
            ->setParameter('fullname', $fullName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Requête de recherche d'un pilote
     * @param ?string $searching
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function findBySearching(?string $searching = null, int $limit = 20, int $offset = 0)
    {
        return $this->getQueryBuilderForSearching($searching)
            ->join('p.birth_city', 'cities')
            ->addSelect('cities')
            ->orderBy('fullname', 'asc')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'élément d'une recherche de pilotes
     * @param ?string $searching
     * @return int
     */
    public function getTotalBySearching(?string $searching) : int
    {
        return $this->getQueryBuilderForSearching($searching)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Pilot[] Returns an array of Pilot objects
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
    public function findOneBySomeField($value): ?Pilot
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
