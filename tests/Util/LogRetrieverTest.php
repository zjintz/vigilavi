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

    protected function mockEntityManager()
    {
        $originA = $this->makeOrigin('192.168.21', 'Sede A', true); 
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([$originA]);
        $originRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($originA);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturn($originRepository);
        $entityManager->expects($this->once())
                      ->method('persist');
        $entityManager->expects($this->once())
                      ->method('flush');
        return $entityManager;
    }

    protected function mockSyslogDBCollectorVoid()
    {
        $syslogDBCollector = $this->createMock(SyslogDBCollector::class);
        return $syslogDBCollector;
    }

    protected function getYesterdayStr()
    {
        $today = new \DateTime();
        $yesterday = $today->sub(new \DateInterval('P1D'));
        return date_format($yesterday, 'yy-m-d');
    }
    protected function mockSyslogDBCollectorYesterday()
    {
        $yesterdayStr = $this->getYesterdayStr();
        $syslogDBCollector = $this->createMock(SyslogDBCollector::class);
        $syslogDBCollector->expects($this->once())
                          ->method('getRemoteLogs')
                          ->with(
                              $this->stringContains($yesterdayStr),
                              $this->anything()
                          )
                          ->willReturn(
                              [[
                                  'ID' => 6284459,
                                  'date' =>$yesterdayStr." 00:00:00",
                                  'log_type' => "Content Filtering",
                                  'log_component' => "HTTP",
                                  'log_subtype' => "Denied",
                                  'fw_rule_id' => 26,
                                  'user_name' => "No Auth",
                                  'user_gp' => "",
                                  'category' =>"IPAddress",
                                  'category_type' => "Acceptable",
                                  'url' => "https://17.167.138.17/",
                                  'src_ip' => "192.168.21.64",
                                  'dst_ip' => "17.167.138.17",
                                  'domain' => "17.167.138.17",
                                  'score_words' => ""
                              ]]
                          );
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
  

    protected function makeLogRetrieverVoid()
    {
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->willReturn($originRepository);

        $logRetriever = new LogRetriever(
            $entityManager,
            $this->mockSyslogDBCollectorVoid(),
        );
        return $logRetriever;
    }

    protected function makeLogRetrieverYesterday()
    {
        $logRetriever = new LogRetriever(
            $this->mockEntityManager(),
            $this->mockSyslogDBCollectorYesterday(),
        );
        return $logRetriever;
    }

    protected function makeLogRetrieverFull()
    {
        $originA = $this->makeOrigin('192.168.21', 'Sede A', true);
        $originC = $this->makeOrigin('192.168.23', 'Sede C', true);
        $originD = $this->makeOrigin('192.168.24', 'Sede D', true);
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->exactly(1))
                         ->method('findBy')
                         ->willReturn([$originA, $originC, $originD]);
        $originRepository->expects($this->exactly(3))
                         ->method('findOneBy')
                         ->will($this->onConsecutiveCalls($originA, $originC, $originD));
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(4))
            ->method('getRepository')
            ->willReturn($originRepository);
        $entityManager->expects($this->exactly(6))
                      ->method('persist');
        $entityManager->expects($this->exactly(3))
                      ->method('flush');

        $logExample = [
            'ID' => 6284459,
            'date' =>"2020-01-01 00:00:00",
            'log_type' => "Content Filtering",
            'log_component' => "HTTP",
            'log_subtype' => "Denied",
            'fw_rule_id' => 26,
            'user_name' => "No Auth",
            'user_gp' => "",
            'category' =>"IPAddress",
            'category_type' => "Acceptable",
            'url' => "https://17.167.138.17/",
            'src_ip' => "192.168.21.64",
            'dst_ip' => "17.167.138.17",
            'domain' => "17.167.138.17",
            'score_words' => ""
        ];
        $syslogDBCollector = $this->createMock(SyslogDBCollector::class);
        $syslogDBCollector->expects($this->exactly(1))
                          ->method('getRemoteLogs')
                          ->withConsecutive(
                              [
                                  $this->stringContains("2020-01-01"),
                                  $this->anything()
                              ],
                              [
                                  $this->stringContains("2020-01-01"),
                                  $this->anything()
                              ],
                              [
                                  $this->stringContains("2020-01-01"),
                                  $this->anything()
                              ]
                          )
                          ->willReturn(
                              [$logExample, $logExample]
                          );
        
        $logRetriever = new LogRetriever(
            $entityManager,
            $syslogDBCollector
        );
        return $logRetriever;
    }

    /**
     * Tests the retrieveData from the LogRetriever when the date given is wrong.
     *
     */
    public function testRetrieveDataWrongDate()
    {
        $logRetriever = new LogRetriever(
            $this->createMock(EntityManagerInterface::class),
            $this->mockSyslogDBCollectorVoid(),
        );
        $response = $logRetriever->retrieveData("20201212");
        $this->assertEquals(
            [
                'error' => 'Date format not supported, the format should be yy-m-d.'
            ],
            $response,
            "retrieveData function of LogRetriever have to return an error message."
        );
        $response = $logRetriever->retrieveData("abc");
        $this->assertEquals(
            [
                'error' => 'Date format not supported, the format should be yy-m-d.'
            ],
            $response,
            "retrieveData function of LogRetriever have to return an error message."
        );
        
    }

    /**
     * Tests the retrieveData from the LogRetriever when there are no Origin 
     * entities stored in the data base.
     *
     */
    public function testRetrieveDataNoOrigins()
    {
        $logRetriever = $this->makeLogRetrieverVoid();
        $response = $logRetriever->retrieveData("2020-01-01");
        $this->assertEquals(
            [
                'date' => "2020-01-01",
                'active_origins' => 0,
                'logs_found' => 0
            ],
            $response,
            "Unexpected output from the retrieveData function, of the LogRetriever."
        );

        $logRetriever = $this->makeLogRetrieverVoid();
        $response = $logRetriever->retrieveData("2020-01-05");
        $this->assertEquals(
            [
                'date' => "2020-01-05",
                'active_origins' => 0,
                'logs_found' => 0
            ],
            $response,
            "Unexpected output from the retrieveData function, of the LogRetriever."
        );
    }

    /**
     * Tests the retrieveData from the LogRetriever when no date is given.
     *
     * In this case it will retrieve the logs from the day before.
     */
    public function testRetrieveDataNoDate()
    {
        $yesterdayStr = $this->getYesterdayStr();
        $logRetriever = $this->makeLogRetrieverYesterday();
        $response = $logRetriever->retrieveData();
        $this->assertEquals(
            [
                'date' => $yesterdayStr,
                'active_origins' => 1,
                'logs_found' => 1
            ],
            $response,
            "Erroneus output from the retrieveData function, from the LogRetriever.");
    }

    /**
     * Tests the retrieveData from the LogRetriever .
     *
     */
    public function testRetrieveData()
    {
        $logRetriever = $this->makeLogRetrieverFull();
        $response = $logRetriever->retrieveData("2020-01-01");
        $this->assertEquals(
            [
                'date' => "2020-01-01",
                'active_origins' => 3,
                'logs_found' => 6
            ],
            $response,
            "Erroneus output from the retrieveData function, from the LogRetriever.");
    }

}
