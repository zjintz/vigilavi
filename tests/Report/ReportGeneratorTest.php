<?php
namespace App\Tests\Report;

use App\Entity\Origin;
use App\Entity\WordSet;
use App\Repository\OriginRepository;
use App\Repository\WordSetRepository;
use App\Report\ReportGenerator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReportGeneratorTest extends TestCase
{
    protected function mockEntityManagerVoid()
    {
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(1))
                      ->method('getRepository')
                      ->withConsecutive([Origin::class])
                      ->willReturnOnConsecutiveCalls($originRepository);

        return $entityManager;
    }

    protected function mockEntityManager()
    {
        //mockin the origin repo
        $originA = $this->makeOrigin('192.168.21', 'Sede A', true); 
        $originRepository = $this->createMock(OriginRepository::class);
        $originRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$originA]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(1))
                      ->method('getRepository')
                      ->withConsecutive([Origin::class])
                      ->willReturnOnConsecutiveCalls($originRepository);
        $entityManager->expects($this->once())
                      ->method('persist');
        $entityManager->expects($this->once())
                      ->method('flush');
        return $entityManager;
    }

    protected function makeOrigin($subnet, $name, $active)
    {
        $origin = new Origin();
        $origin->setSubnet($subnet);
        $origin->setName($name);
        $origin->setActive($active);
        return $origin;
    }
  
    protected function makeReportGeneratorVoid()
    {        
        $reportGenerator = new ReportGenerator(
            $this->mockEntityManagerVoid(),
        );
        return $reportGenerator;
    }

    protected function makeReportGeneratorFull()
    {
        $reportGen = new ReportGenerator(
            $this->mockEntityManager()
        );
        return $reportGen;
    }

    /**
     * Tests the generateAllReports method when the given date is in wrong format.
     *
     */
    public function testGenerateAllReportsWrongDate()
    {
        $reportGenerator = new ReportGenerator(
            $this->createMock(EntityManagerInterface::class)
        );
        $response = $reportGenerator->generateAllReports("20201212");
        $this->assertEquals(
            [
                'error' => 'Date format not supported, the format should be yy-m-d.'
            ],
            $response,
            "Error message expected."
        );
        $response = $reportGenerator->generateAllReports("abc");
        $this->assertEquals(
            [
                'error' => 'Date format not supported, the format should be yy-m-d.'
            ],
            $response,
            "Error message expected."
        );
      
    }
    /**
     * Tests the generateAllReports method when there are no origins or wordsets.
     *
     */
    public function testGenerateAllReportsVoid()
    {
        $reportGenerator = $this->makeReportGeneratorVoid();
        $response = $reportGenerator->generateAllReports();
        $this->assertEquals(
            [
                'total' => 0
            ],
            $response
        );
        $reportGenerator = $this->makeReportGeneratorVoid();
        $response = $reportGenerator->generateAllReports("2020-01-01");
        $this->assertEquals(
            [
                'total' => '0'
            ],
            $response
        );
        
    }

    /**
     * Tests the generateAllReports method.
     *
     */
    public function testGenerateAllReports()
    {
        $reportGenerator = $this->makeReportGeneratorFull();
        $response = $reportGenerator->generateAllReports();
        $this->assertEquals(
            [
                'total' => 1
            ],
            $response
        );
    }
    
}
