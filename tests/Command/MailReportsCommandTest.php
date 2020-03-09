<?php

namespace App\Tests\Command;

use App\Command\MailReportsCommand;
use App\DataFixtures\UserNoActiveSubsTestFixtures;
use App\DataFixtures\AppExampleFixtures;
use App\DataFixtures\UserTestFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;


/**
 * Tests the vigilavi mail-repots command.
 *
 *
 */
class MailReportsCommandTest extends WebTestCase
{
    use FixturesTrait;
    
    protected $command;
    protected $commandTester;


    public function testExecuteWrongDateGiven()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('vigilavi:mail-reports');
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
    
    public function testExecuteNoUsers()
    {
        $this->loadFixtures([]);
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('vigilavi:mail-reports');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'There are no enabled users in the DB.',
            $output
        );
    }

    public function testExecuteNoActiveUsers()
    {
        $this->loadFixtures([UserNoActiveSubsTestFixtures::class]);
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('vigilavi:mail-reports');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            "date" => "2020-01-01"
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('There are no users with active email subscriptions for the given date', $output);
    }

    public function testExecute()
    {
        $this->loadFixtures(
            [AppExampleFixtures::class, UserTestFixtures::class]
        );

        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('vigilavi:mail-reports');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'Sending Reports ...',
            $output
        );
        $this->assertStringContainsString(
            '        - Sending reports to user@test.com',
            $output
        );
        $this->assertStringContainsString(
            '        - Sending reports to editor@test.com', $output
        );
        $this->assertStringContainsString(
            '        - Sending reports to admin@test.com',
            $output
        );
        $this->assertStringContainsString('Done.', $output);
    }


}
