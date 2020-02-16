<?php
namespace App\Tests\Util;

use App\Entity\Origin;
use App\Entity\LogEntry;
use App\Repository\OriginRepository;
use App\Repository\LogRepository;
use App\Util\SyslogDBCollector;
use App\Util\LogRetriever;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LogRetrieverTest extends TestCase
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
        /*        $syslogDBCollector->expects($this->once())
            ->method('getRemoteOrigins')
            ->willReturn([]);*/
        return $syslogDBCollector;
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

    protected function makeLogRetrieverVoid()
    {
        $syslogDBCollector = $this->mockSyslogDBCollectorVoid();
        $oRetriever = new LogRetriever(
            $this->mockEntityManagerVoid(),
            $syslogDBCollector,
        );
        return $oRetriever;
    }

    /**
     * Tests the retrieveData from the LogRetriever when the date given is wrong.
     *
     */
    public function testRetrieveDataWrongDate()
    {
        $logRetriever = $this->makeLogRetrieverVoid();
        $response = $logRetriever->retrieveData("20201212");
        $this->assertEquals(
            ['error' => 'Date format not supported, the format should be yy-m-d.'],
            $response,
            "Erroneus output from the retrieveData function, from the LogRetriever.");
    }

    /**
     * Tests the retrieveData from the LogRetriever when the date given is wrong.
     *
     */
    public function testRetrieveDataNoDate()
    {
        $logRetriever = $this->makeLogRetrieverVoid();
        $response = $logRetriever->retrieveData();
        $this->assertEquals(
            ['error' => 'Date format not supported, the format should be yy-m-d.'],
            $response,
            "Erroneus output from the retrieveData function, from the LogRetriever.");
    }

}
