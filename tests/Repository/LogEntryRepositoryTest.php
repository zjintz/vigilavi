<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserTestFixtures;
use App\DataFixtures\AppExampleFixtures;
use App\Entity\LogEntry;
use App\Entity\Report;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests of the user registration.
 *
 */
class LogEntryRepositoryTest extends WebTestCase
{
    use FixturesTrait;
    
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

     /**
     * Test the repository brings the Entries of a report that should have no entries.
     */
    public function testFindEntriesToReportVoid()
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        $reportId = $fixtures->getReference('report-30-2')->getId();
        $report = $this->entityManager
                ->getRepository(Report::class)
                ->findOneBy(["id" => $reportId]);
        $entries = $this->entityManager
            ->getRepository(LogEntry::class)
            ->findEntriesToReport($report)
        ;
        //this has no entries! Macondo for 2019-09-30
        $this->assertSame(0, count($entries));
    }


    /**
     * Test the repository brings the Entries of a report.
     */
    public function testFindEntriesToReport()
    {
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();

        //report-23-0 belongs to Comala should have 1 entry for this date
        $reportId = $fixtures->getReference('report-23-0')->getId();
        $report = $this->entityManager
                ->getRepository(Report::class)
                ->findOneBy(["id" => $reportId]);
        $entries = $this->entityManager
            ->getRepository(LogEntry::class)
            ->findEntriesToReport($report)
        ;
        $this->assertSame(1, count($entries));
        
        //report-23-1 belongs to macondo should have 3 entries
        $reportId = $fixtures->getReference('report-23-1')->getId();
        $report = $this->entityManager
                ->getRepository(Report::class)
                ->findOneBy(["id" => $reportId]);
        $entries = $this->entityManager
            ->getRepository(LogEntry::class)
            ->findEntriesToReport($report)
        ;
        $this->assertSame(3, count($entries));
        
        //report-23-2 belongs to Area51 should have 992  entries for this date
        $reportId = $fixtures->getReference('report-23-2')->getId();
        $report = $this->entityManager
                ->getRepository(Report::class)
                ->findOneBy(["id" => $reportId]);
        $entries = $this->entityManager
            ->getRepository(LogEntry::class)
            ->findEntriesToReport($report)
        ;
        $this->assertSame(992, count($entries));
    }
}
