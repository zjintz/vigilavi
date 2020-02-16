<?php

namespace App\Util;

use App\Entity\Origin;
use App\Repository\OriginRepository;
use App\Util\OriginSynchronizer;
use App\Util\SyslogDBCollector;
use Doctrine\ORM\EntityManagerInterface;

/**
 * \brief     Retrieves and syncronizes data from the origins.
 *
 *
 */
class OriginRetriever
{

    private $entityManager;
    private $syslogDBCollector;
    private $originSynchronizer;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        SyslogDbCollector $syslogDBCollector,
        OriginSynchronizer $originSynchronizer
    ) {
        $this->entityManager = $entityManager;
        $this->syslogDBCollector = $syslogDBCollector;
        $this->originSynchronizer = $originSynchronizer;
    }
    
    /**
     * Retrieves the origins data, and syncs it with the DB.
     *
     */
    public function retrieveData()
    {
        $remoteOrigins = $this->syslogDBCollector->getRemoteOrigins();
        $localOrigins = $this->entityManager->getRepository(Origin::class)->findAll();
        if (empty($remoteOrigins) && empty($localOrigins)) {
            return
                [
                    'new_origins' => 0,
                    'modified_origins' =>0,
                    'active_origins'=>0,
                    'inactive_origins' =>0,
                    'total_origins' =>0
                ];
        }
        $updatedOrigins = $this->originSynchronizer->syncOrigins(
            $localOrigins,
            $remoteOrigins,
        );
        foreach($updatedOrigins['entities'] as $origin) {
            $this->entityManager->persist($origin);
            
        }
        $this->entityManager->flush();
        return $updatedOrigins['summary'];
    }
}
