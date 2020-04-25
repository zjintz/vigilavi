<?php

namespace App\Util;

use App\Entity\Origin;
use App\Entity\LogEntry;
use App\Repository\OriginRepository;
use App\Util\SyslogDBCollector;
use Doctrine\ORM\EntityManagerInterface;

/**
 * \brief     Retrieves and syncronizes data from the origins.
 *
 *
 */
class LogRetriever
{

    private $entityManager;
    private $syslogDBCollector;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        SyslogDbCollector $syslogDBCollector
    ) {
        $this->entityManager = $entityManager;
        $this->syslogDBCollector = $syslogDBCollector;
    }
    
    /**
     * Retrieves the logs data, and syncs it with the DB.
     *
     */
    public function retrieveData(string $dateLog = null)
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $date = \DateTime::createFromFormat('yy-m-d', $dateLog);

        if (!$date && !is_null($dateLog)) {
            return ['error' => 'Date format not supported, the format should be yy-m-d.'];
        }

        $origins = $this->entityManager->getRepository(Origin::class)->findBy(
            ["active"=>true]
        );
        if (empty($origins)) {
            return [
                'date' => $dateLog,
                'active_origins' => 0,
                'logs_found' => 0
            ];
        }
        if (is_null($dateLog))
        {
            $today = new \DateTime();
            $yesterday = $today->sub(new \DateInterval('P1D'));
            $dateLog =  date_format($yesterday, 'yy-m-d');
        }
        $logsFound = $this->fetchLogsByOrigin($dateLog, $origins);

        return [
            'date' => $dateLog,
            'active_origins' => count($origins),
            'logs_found' => $logsFound
        ];
    }

    protected function fetchLogsByOrigin($dateLog, $origins)
    {
        $logsFound = 0;
        $cycle = 1;
        $batchSize = 15000;
        while (true) {
            $end = $batchSize * $cycle;
            $start =  $end - $batchSize;
            $remoteLogs = $this->syslogDBCollector->getRemoteLogs(
                $dateLog,
                $origins,
                $start,
                $batchSize
            );
            $logsFound += count($remoteLogs);
            if (empty($remoteLogs)) {
                break;
            }
            $cycle += 1 ;
            foreach ($origins as $origin) {
                $this->persistLogsByOrigin($origin->getId(), $remoteLogs);
            }
            unset($remoteLogs);
            gc_collect_cycles();
        }
        return $logsFound;
    }

    protected function persistLogsByOrigin($originId, $remoteLogs)
    {
        $origin = $this->entityManager->getRepository(Origin::class)->findOneBy(
            ["id"=>$originId]
        );
        
        $counter = 0 ;
        foreach ($remoteLogs as $log) {
            if (!$this->logBelongsToOrigin($log, $origin)) {
                continue;
            }
            $this->entityManager->persist($this->createLogEntry($log, $origin));
            $counter+=1;
            //batching!!
        }
        $this->entityManager->flush();
        $this->entityManager->clear();
        $remoteLogs = null;
        $origin = null;
        unset($remoteLogs);
        unset($origin);
        gc_collect_cycles();
        return $counter;
    }

    protected function logBelongsToOrigin(array $log, Origin $origin)
    {
        $subnet = $origin->getSubnet();
        if ($subnet === substr($log["src_ip"], 0, strlen($subnet))) {
            return true;
        }
        return false;
    }
    
    protected function createLogEntry(array $log, Origin $origin): LogEntry
    {
        $logEntry = new LogEntry();
        $logEntry->setOrigin($origin);
        $logEntry->setLogType($log['log_type']);
        $logEntry->setLogSubtype($log['log_subtype']);
        $logEntry->setUserName($log['user_name']);
        $logEntry->setUrl($log['url']);
        $logEntry->setSrcIp($log['src_ip']);
        $logEntry->setDstIp($log['dst_ip']);
        $logEntry->setDomain($log['domain']);
        $format = 'Y-m-d H:i:s';
        $date = \DateTime::createFromFormat($format, $log['date']);
        $logEntry->setDate($date);
        return $logEntry;
    }
}
