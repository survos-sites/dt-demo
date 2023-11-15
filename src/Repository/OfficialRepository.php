<?php

namespace App\Repository;

use App\Entity\Official;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Official>
 *
 * @method Official|null find($id, $lockMode = null, $lockVersion = null)
 * @method Official|null findOneBy(array $criteria, array $orderBy = null)
 * @method Official[]    findAll()
 * @method Official[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Official::class);
    }

//    /**
//     * @return Official[] Returns an array of Official objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Official
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
