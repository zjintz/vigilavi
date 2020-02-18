<?php

namespace App\Command;

use App\Util\SyslogDBCollector;
use App\Util\OriginRetriever;
use App\Util\LogRetriever;
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
    protected $originRetriever;
    protected $logRetriever;

    public function __construct(
        SyslogDBCollector $syslogDBCollector,
        OriginRetriever $originRetriever,
        LogRetriever $logRetriever
    ) {
        $this->syslogDBCollector = $syslogDBCollector;
        $this->originRetriever = $originRetriever;
        $this->logRetriever = $logRetriever;
        
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
        ]);
        $originSummary = $this->originRetriever->retrieveData();
        foreach ($originSummary as $key => $value) {
            $output->writeln(["      ".$key." : ". $value]);
        }

        //        $logSummary = $this->logRetriever->retrieveData();
        $output->writeln([
            '----- Sync origins done',
            '----- Sync logs',
            '----- Sync logs done',
        ]);

        $io = new SymfonyStyle($input, $output);
        $io->success('Sync data finished.');
        return 0;
    }
}
