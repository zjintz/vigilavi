<?php

namespace App\Command;

use App\Util\SyslogDBCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This Command syncs the remote database.
 * It  brings  the log entries and the origins.
 */
class SyncDataCommand extends Command
{
    protected static $defaultName = 'vigilavi:sync-data';

    protected $syslogDBCollector;

    public function __construct(SyslogDBCollector $syslogDBCollector)
    {
        $this->syslogDBCollector = $syslogDBCollector;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setDescription('This Command brings the data from the remote Database related to sophos events and origins.')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date of the entry logs to sync. The format is "yyyy-mm-dd". If no date is given it will sync today\'s date.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $syncDate = $input->getArgument('date');
        if (!$syncDate) {
            $syncDate = (new \DateTime())->format('yy-m-d');
        }
        $output->writeln([
            'Sync Data',
            '=========',
            '',
            'Sync vigilavi data from remote database:',
            '    - Date: '.$syncDate,
            '----- Sync origins',
            '----- Sync origins done',
            '----- Sync logs',
            '----- Sync logs done',
        ]);

        $this->syslogDBCollector->getRemoteLogs();
        $io = new SymfonyStyle($input, $output);
        $io->success('Sync data finished.');
        return 0;
    }
}
