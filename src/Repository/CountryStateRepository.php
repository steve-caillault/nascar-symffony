<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
/***/
use App\Entity\{
    Country,
    CountryState
};

/**
 * @method CountryState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryState[]    findAll()
 * @method CountryState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CountryStateRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryState::class);
    }

    /**
     * Retourne le nombre total d'état du pays en paramètre
     * @param Country $country
     * @return int
     */
    public function getTotalFrom(Country $country) : int
    {
        $dql = strtr('SELECT COUNT(countries_states.code) FROM :object countries_states WHERE countries_states.country = :country', [
            ':object' => CountryState::class,
        ]);
        $total = (int) $this->getEntityManager()->createQuery($dql)
            ->setParameter('country', $country)
            ->getSingleScalarResult();
        return $total;
    }
}
