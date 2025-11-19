<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class WineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry){ 
    parent::__construct($registry, Wine::class); 
    }
}