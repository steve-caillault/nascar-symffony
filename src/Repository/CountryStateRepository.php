<?php

namespace App\Repository;

use App\Entity\CountryState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CountryState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryState[]    findAll()
 * @method CountryState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryState::class);
    }
}
