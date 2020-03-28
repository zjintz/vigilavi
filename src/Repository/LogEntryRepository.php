<?php

namespace App\Repository;

use App\Entity\LogEntry;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogEntry[]    findAll()
 * @method LogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }

    /**
     * @return LogEntry[] Returns an array of LogEntry objects
     */
    public function findEntriesToReport($report)
    {
        $dateTime = $report->getDate();
        $qBuilder = $this->createQueryBuilder('l');
        $qBuilder->andWhere('l.date BETWEEN :dateMin AND :dateMax')
                 ->setParameters(
                     [
                         'dateMin' => $dateTime->format('Y-m-d 00:00:00'),
                         'dateMax' => $dateTime->format('Y-m-d 23:59:59'),
                     ]
                 );
        $qBuilder->andWhere('l.origin = :val')
                 ->setParameter('val', $report->getOrigin());

        return $qBuilder->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?LogEntry
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
