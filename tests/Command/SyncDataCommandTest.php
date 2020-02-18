<?php

namespace App\Tests\Command;

use App\Util\OriginRetriever;
use App\Util\LogRetriever;
use App\Command\SyncDataCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


/**
 * Tests the vigilavi sync-data command.
 *
 *
 */
class SyncDataCommandTest extends KernelTestCase
{
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
        $application->add(new SyncDataCommand($oRetriever, $logRetriever));

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
        $this->assertSuccessDisplay();
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
