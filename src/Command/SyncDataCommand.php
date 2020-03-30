<?php

namespace App\Command;

use App\Util\OriginRetriever;
use App\Util\LogRetriever;
use App\Report\ReportGenerator;
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
    protected $originRetriever;
    protected $logRetriever;
    protected $reportGenerator;

    public function __construct(
        OriginRetriever $originRetriever,
        LogRetriever $logRetriever,
        ReportGenerator $reportGenerator
    ) {
        $this->originRetriever = $originRetriever;
        $this->logRetriever = $logRetriever;
        $this->reportGenerator = $reportGenerator;
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setDescription('This Command brings the data from the remote Database related to sophos events and origins.')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date of the entry logs to sync. The format is "yyyy-mm-dd". If no date is given it will sync yesterda\'s date.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inout = new SymfonyStyle($input, $output);
        $syncDate = $input->getArgument('date');
        if (!$syncDate) {
            $today = new \DateTime();
            $yesterday = $today->sub(new \DateInterval('P1D'));
            $syncDate = $yesterday->format('yy-m-d');
        }
        if (!$this->validateDate($syncDate)) {
            $inout->error('Wrong date format; Please used date format: yyyy-mm-dd.');
            return 0;
        }
        
        $output->writeln([
            'Sync Data',
            '=========',
            '',
            'Sync vigilavi data from remote database:',
            '    - Date: '.$syncDate,
            '----- Sync origins',
        ]);
        // first bring the origins
        $originSummary = $this->originRetriever->retrieveData();
        foreach ($originSummary as $key => $value) {
            $output->writeln(["      ".$key." : ". $value]);
        }
        $output->writeln([
            '----- Sync origins done',
            '----- Sync logs']
        );
        // now the logs
        $logSummary = $this->logRetriever->retrieveData($syncDate);
        foreach ($logSummary as $key => $value) {
            $output->writeln(["      ".$key." : ". $value]);
        }

        $output->writeln([
            '----- Sync logs done',
        ]);
        ///////
        $output->writeln([
            '----- Creating Reports',
        ]);
        
        $newReports = 0;
        $newReports = $this->reportGenerator->generateAllReports($syncDate)['total'];
        $output->writeln([
            '      Total new reports: '.$newReports,
        ]);
        $output->writeln([
            '----- Reports Created',
        ]);
        
        
        $inout->success('Sync data finished.');
        return 0;
    }

    protected function validateDate($dateString, $format = 'Y-m-d')
    {
        $date = \DateTime::createFromFormat($format, $dateString);
        return $date && $date->format($format) === $dateString;
    }
}
