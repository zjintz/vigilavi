<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\ViewByWord;
use App\Entity\WordStat;
use App\Report\OutcomeGenerator;
use App\Report\ViewByWordMaker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Twig\Environment;

class ReportAnalizer
{
    private $entityManager;
    private $outcomeGenerator;
    private $viewMaker;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        OutcomeGenerator $outcomeGenerator,
        ViewByWordMaker $viewMaker
    ) {
        $this->entityManager = $entityManager;
        $this->outcomeGenerator = $outcomeGenerator;
        $this->viewMaker = $viewMaker;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function addReportData(Report $report, LifecycleEventArgs $event)
    {
        $entriesRepo = $this->entityManager->getRepository(LogEntry::class);
        $entries = $entriesRepo->findBy(['date'=>$report->getDate()]);
        $report  = $this->outcomeGenerator->genOutcomes($report, $entries);
        $viewByWord  = $this->viewMaker->makeView($report);
        $report->setViewByWord($viewByWord);
    }
}
