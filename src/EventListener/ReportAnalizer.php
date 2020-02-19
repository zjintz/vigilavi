<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\ViewByWord;
use App\Entity\WordStat;
use App\Util\ReportAnalizerAux;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Twig\Environment;

class ReportAnalizer
{
    private $entityManager;
    private $auxiliar;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        ReportAnalizerAux $auxiliar
    ) {
        $this->entityManager = $entityManager;
        $this->auxiliar = $auxiliar;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function addReportData(Report $report, LifecycleEventArgs $event)
    {
        $entriesRepo = $this->entityManager->getRepository(LogEntry::class);
        $entries = $entriesRepo->findBy(['date'=>$report->getDate()]);
        $report  = $this->auxiliar->genReportOutcomes($report, $entries);
    }
}
