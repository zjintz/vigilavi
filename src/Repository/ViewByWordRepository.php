<?php

namespace App\Repository;

use App\Entity\ViewByWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ViewByWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method ViewByWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method ViewByWord[]    findAll()
 * @method ViewByWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewByWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ViewByWord::class);
    }

    // /**
    //  * @return ViewByWord[] Returns an array of ViewByWord objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ViewByWord
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
