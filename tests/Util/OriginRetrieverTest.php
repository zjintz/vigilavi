<?php
namespace App\Tests\Util;

use App\Entity\Origin;
use App\Repository\OriginRepository;
use App\Util\OriginSynchronizer;
use App\Util\SyslogDBCollector;
use App\Util\OriginRetriever;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class OriginRetrieverTest extends TestCase
{
    protected function mockEntityManagerVoid()
    {
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($originRepository);
        return $entityManager;
    }

    protected function mockSyslogDBCollectorVoid()
    {
        $syslogDBCollector = $this->createMock(SyslogDBCollector::class);
        $syslogDBCollector->expects($this->once())
            ->method('getRemoteOrigins')
            ->willReturn([]);
        return $syslogDBCollector;
    }

    protected function makeOriginRetrieverVoid()
    {
        $syslogDBCollector = $this->mockSyslogDBCollectorVoid();
        $originSynchronizer = $this->createMock(OriginSynchronizer::class);
        $oRetriever = new OriginRetriever(
            $this->mockEntityManagerVoid(),
            $syslogDBCollector,
            $originSynchronizer,
        );
        return $oRetriever;
    }

    protected function makeOriginRetrieverEmptyFull()
    {
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
                         ->method('findAll')
                         ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($originRepository);
        $entityManager->expects($this->exactly(3))
                      ->method('persist');
        $entityManager->expects($this->once())
                      ->method('flush');
        $remoteOrigins = [];
        $remoteOrigins[]= ['subnet'=>'192.168.1', 'nomeSede'=> 'sedeA', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.2', 'nomeSede'=> 'sedeB', 'red'=>1];
        $remoteOrigins[]= ['subnet'=>'192.168.3', 'nomeSede'=> 'sedeC', 'red'=>1];
        $syslogDBCollector = $this->createMock(SyslogDBCollector::class);
        $syslogDBCollector->expects($this->once())
                          ->method('getRemoteOrigins')
                          ->willReturn($remoteOrigins);
        $newOrigins = [];
        $newOrigins[]= $this->makeOrigin("192.168.1", 'sedeA', true);
        $newOrigins[]= $this->makeOrigin("192.168.2", 'sedeB', true);
        $newOrigins[]= $this->makeOrigin("192.168.3", 'sedeC', true);
        $summary = [
            'new_origins' => 3,
            'modified_origins' => 0,
            'active_origins'=> 3,
            'inactive_origins' =>0,
            'total_origins' =>3
        ];
        $originSynchronizer = $this->createMock(OriginSynchronizer::class);
        $originSynchronizer->expects($this->once())
                           ->method('syncOrigins')
                           ->willReturn(
                               ['entities'=>$newOrigins , 'summary'=>$summary]
                           );
        $oRetriever = new OriginRetriever(
            $entityManager,
            $syslogDBCollector,
            $originSynchronizer,
        );
        return $oRetriever;
    }

    protected function makeOrigin($subnet, $name, $active)
    {
        $origin = new Origin();
        $origin->setSubnet($subnet);
        $origin->setName($name);
        $origin->setActive($active);
        return $origin;
    }
    
    protected function makeOriginRetrieverFullEmpty()
    {
        $origins = [];
        $origins[]= $this->makeOrigin("192.168.1", 'origin-A', true);
        $origins[]= $this->makeOrigin("192.168.2", 'origin-B', true);
        $origins[]= $this->makeOrigin("192.168.3", 'origin-C', true);
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
                         ->method('findAll')
                         ->willReturn($origins);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($originRepository);
        $entityManager->expects($this->exactly(3))
                      ->method('persist');
        $entityManager->expects($this->once())
                      ->method('flush');
        $newOrigins = [];
        $newOrigins[]= $this->makeOrigin("192.168.1", 'origin-A', false);
        $newOrigins[]= $this->makeOrigin("192.168.2", 'origin-B', false);
        $newOrigins[]= $this->makeOrigin("192.168.3", 'origin-C', false);
        $syslogDBCollector = $this->mockSyslogDBCollectorVoid();
        $summary = [
            'new_origins' => 0,
            'modified_origins' =>3,
            'active_origins'=>0,
            'inactive_origins' =>3,
            'total_origins' =>3
        ];
        $originSynchronizer = $this->createMock(OriginSynchronizer::class);
        $originSynchronizer->expects($this->once())
                           ->method('syncOrigins')
                           ->willReturn(
                               ['entities'=>$newOrigins , 'summary'=>$summary]
                           );
        $oRetriever = new OriginRetriever(
            $entityManager,
            $syslogDBCollector,
            $originSynchronizer,
        );
        return $oRetriever;
    }

    /**
     * Tests the retrieveData from the OriginRetriever when there is nothing in 
     * the vigiliavi DB, and nothing in the syslog DB.
     *
     */
    public function testRetrieveOriginsEmptyEmpty()
    {
        $oRetriever = $this->makeOriginRetrieverVoid();
        $response = $oRetriever->retrieveData();
        $this->assertEquals(
            ['new_origins' => 0, 'modified_origins' =>0, 'active_origins'=>0, 'inactive_origins' =>0, 'total_origins' =>0],
            $response,
            "Erroneus output from the retrieveData function.");
    }

    /**
     * Tests the retrieveData from the OriginRetriever when there are 
     * origin entities in the vigiliavi DB, and nothing in the syslog DB.
     *
     * In this case every origin in vigilavi DB is set to inactive. 
     */
    public function testRetrieveOriginsFullEmpty()
    {
        $oRetriever = $this->makeOriginRetrieverFullEmpty();
        $response = $oRetriever->retrieveData();
        $this->assertEquals(
            [
                'new_origins' => 0,
                'modified_origins' =>3,
                'active_origins'=>0,
                'inactive_origins' =>3,
                'total_origins' =>3
            ],
            $response,
            "Erroneus output from the retrieveData function."
        );
    }
    
    /**
     * Tests the retrieveData from the OriginRetriever when  
     * the vigilavi DB is empty, but there are records in the syslog DB.
     *
     */
    public function testRetrieveOriginsEmptyFull()
    {
        $oRetriever = $this->makeOriginRetrieverEmptyFull();
        $response = $oRetriever->retrieveData();
        $this->assertEquals(
            [
                'new_origins' => 3,
                'modified_origins' => 0,
                'active_origins'=> 3,
                'inactive_origins' =>0,
                'total_origins' =>3
            ],
            $response,
            "Erroneus output from the retrieveData function."
        );
    }
}
