<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class SyncDataCommandTest extends KernelTestCase
{

    public function testExecuteNoDateGiven()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('vigilavi:sync-data');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $testDate = (new \DateTime())->format('yy-m-d');
        $this->assertStartDisplay($commandTester, $testDate);
        $this->assertSuccessDisplay($commandTester);
    }
    
    public function testExecuteDateGiven()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('vigilavi:sync-data');
        $commandTester = new CommandTester($command);
        $testDate = '2020-02-10';
        $commandTester->execute([
            'date' => $testDate, // pass arguments to the helper
            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);
        $this->assertStartDisplay($commandTester, $testDate);
        $this->assertSuccessDisplay($commandTester);
        // the output of the command in the console

    }

    private function assertStartDisplay($commandTester, $dateGiven)
    {
        $output = $commandTester->getDisplay();
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

    private function assertSuccessDisplay($commandTester)
    {
        $output = $commandTester->getDisplay();
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
