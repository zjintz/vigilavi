<?php

namespace App\Tests\Command;

use App\DataFixtures\AppExampleFixtures;
use App\DataFixtures\UserTestFixtures;
use App\Entity\Report;
use App\Util\OriginRetriever;
use App\Util\LogRetriever;
use App\Report\ReportGenerator;
use App\Command\SyncDataCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Tests the vigilavi sync-data command.
 *
 *
 */
class SyncDataCommandTest extends KernelTestCase
{
    use FixturesTrait;

    protected $command;
    protected $commandTester;
    
    protected function customSetUp():void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $oRetriever = $this->createMock(OriginRetriever::class);
        $oRetriever->expects($this->once())
            ->method('retrieveData')
            ->willReturn([
                'new_origins' => 0,
                'modified_origins' =>3,
                'active_origins'=>0,
                'inactive_origins' =>3,
                'total_origins' =>3
            ]);
        
        $logRetriever = $this->createMock(LogRetriever::class);
        $logRetriever->expects($this->once())
                   ->method('retrieveData')
                   ->willReturn([
                       'date' => '',
                       'active_origins' => 1,
                       'logs_found' => 1   
                   ]);
        $reportGenerator = $this->createMock(ReportGenerator::class);
        $reportGenerator->expects($this->once())
                   ->method('generateAllReports')
                   ->willReturn([
                       'total' => 0,
                   ]);
        $application->add(
            new SyncDataCommand($oRetriever, $logRetriever, $reportGenerator)
        );

        $this->command = $application->find('vigilavi:sync-data');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWrongDateGiven()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('vigilavi:sync-data');
        $commandTester = new CommandTester($command);
        $wrongDate = "23621";
        $commandTester->execute([
            "date" => $wrongDate
        ]);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'Wrong date format; Please used date format: yyyy-mm-dd.',
            $output
        );
    }
    
    public function testExecuteNoDateGiven()
    {
        $this->customSetUp();
        $this->commandTester->execute([]);
        $today = new \DateTime();
        $yesterday = $today->sub(new \DateInterval('P1D'));
        $testDate =  $yesterday->format('yy-m-d');
        $this->assertStartDisplay($testDate);
        $this->assertMakingReportsDisplay();
        $this->assertSuccessDisplay();
    }
    
    public function testExecuteDateGiven()
    {
        $this->customSetUp();
        $testDate = '2020-02-10';
        $this->commandTester->execute([
            'date' => $testDate, // pass arguments to the helper
            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);
        $this->assertStartDisplay($testDate);
        $this->assertMakingReportsDisplay();
        $this->assertSuccessDisplay();
    }


    protected function specialSetup($entityManager, $kernel)
    {      
        $application = new Application($kernel);
        $fixtures = $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        )->getReferenceRepository();
        $oRetriever = $this->createMock(OriginRetriever::class);
        $oRetriever->expects($this->once())
            ->method('retrieveData')
            ->willReturn([
                'new_origins' => 0,
                'modified_origins' =>3,
                'active_origins'=>0,
                'inactive_origins' =>3,
                'total_origins' =>3
            ]);
        
        $logRetriever = $this->createMock(LogRetriever::class);
        $logRetriever->expects($this->once())
                   ->method('retrieveData')
                   ->willReturn([
                       'date' => '',
                       'active_origins' => 1,
                       'logs_found' => 1   
                   ]);
        
        $reportGenerator = new ReportGenerator($entityManager);
        $application->add(
            new SyncDataCommand($oRetriever, $logRetriever, $reportGenerator)
        );

        $this->command = $application->find('vigilavi:sync-data');
        
        $this->commandTester = new CommandTester($this->command);
    }
    
    /**
     * In this tests the DataFixtures are loaded. And a real ReportGenerator is used.
     *
     *
     */
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $container = self::$container;
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $this->specialSetUp($entityManager, $kernel);
        $testDate = '2019-08-22';
        $this->commandTester->execute([
            'date' => $testDate, // pass arguments to the helper
            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);
        $this->assertStartDisplay($testDate);
        $this->assertMakingReportsDisplay(3);
        $this->assertSuccessDisplay();
        $newDate = \DateTime::createFromFormat("Y-m-d", "2019-08-22");
        $reports = $entityManager->getRepository(Report::class)->findByDate($newDate);
        $this->assertEquals(3, count($reports));
        //lets make sure it generates some reports and the views.
        $report22 = $reports[1];
        $this->assertEquals("Macondo", $report22->getOrigin()->getName());
        $this->assertEquals("English words", $report22->getOrigin()->getWordSets()[0]->getName());
        $this->assertEquals(12, count($report22->getViewByWord()->getWordStats()));
    }

    private function assertStartDisplay($dateGiven)
    {
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Sync vigilavi data from remote database',
            $output
        );
        $this->assertStringContainsString(
            'Date: '. $dateGiven,
            $output
        );
        $this->assertStringContainsString(
            '----- Sync origins',
            $output
        );
        $this->assertStringContainsString(
            '----- Sync logs',
            $output
        );
    }

    private function assertMakingReportsDisplay($newReports = 0)
    {
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            '----- Creating Reports',
            $output
        );
        $this->assertStringContainsString(
            '      Total new reports: '.$newReports,
            $output
        );
        $this->assertStringContainsString(
            '----- Reports Created',
            $output
        );
    }

    private function assertSuccessDisplay()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Sync data finished',
            $output
        );

        $this->assertStringContainsString(
            '----- Sync origins done',
            $output
        );
        $this->assertStringContainsString(
            '----- Sync logs done',
            $output
        );

        
    }

}
