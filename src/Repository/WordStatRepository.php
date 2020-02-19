<?php

namespace App\Repository;

use App\Entity\WordStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WordStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method WordStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method WordStat[]    findAll()
 * @method WordStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordStat::class);
    }

    // /**
    //  * @return WordStat[] Returns an array of WordStat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WordStat
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
