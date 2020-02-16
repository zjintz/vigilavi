<?php

namespace App\Util;

use App\Entity\Origin;
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
    public function retrieveData()
    {
        $localOrigins = $this->entityManager->getRepository(Origin::class)->findAll();
        return ['error' => 'Date format not supported, the format should be yy-m-d.'];
    }
}
