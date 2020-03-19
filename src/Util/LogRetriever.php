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
        $date = \DateTime::createFromFormat('yy-m-d', $dateLog);

        if (!$date && !is_null($dateLog)) {
            return ['error' => 'Date format not supported, the format should be yy-m-d.'];
        }

        $origins = $this->entityManager->getRepository(Origin::class)->findBy(["active"=>true]);
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
        $logsFound= 0;
        foreach ($origins as $origin) {
            $logsFound += $this->fetchLogsByOrigin($dateLog, $origin);
        }
        return [
            'date' => $dateLog,
            'active_origins' => count($origins),
            'logs_found' => $logsFound
        ];
    }


    protected function fetchLogsByOrigin($dateLog, $origin)
    {
        $remoteLogs = $this->syslogDBCollector->getRemoteLogs(
            $dateLog,
            $origin->getSubnet()
        );
        foreach ($remoteLogs as $log) {
            $logEntry = $this->createLogEntry($log, $origin);
            $this->entityManager->persist($logEntry);
        }
        $this->entityManager->flush();
        $this->entityManager->clear();
        return count($remoteLogs);
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
