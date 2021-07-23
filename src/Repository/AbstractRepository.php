<?php

/**
 * Repository de base
 */

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository implements EntityRepositoryInterface
{

}