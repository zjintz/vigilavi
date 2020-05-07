<?php

namespace App\Command;

use App\Report\ReportMailer;
use App\Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to send vigilavi reports.
 *
 *
 */
class MailReports extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'vigilavi:mail-reports';

    private $reportMailer;
    
    public function __construct(
        ReportMailer $reportMailer
    ) {
        $this->reportMailer = $reportMailer;
        parent::__construct();
    }
        
    
    protected function configure()
    {
        
        $this
             ->setDescription('Sends the vigilavi reports.')
             ->setHelp(
                 'Search in the DB for active users, and according to their email subscription send the vigilavi reports.'
             )
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date of the date of reports to send. The format is "yyyy-mm-dd". If no date is given it will sync yesterda\'s date.'
            );

 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inout = new SymfonyStyle($input, $output);
        $reportsDate = $input->getArgument('date');
        if (!$reportsDate) {
            $today = new \DateTime();
            $yesterday = $today->sub(new \DateInterval('P1D'));
            $reportsDate = $yesterday->format('yy-m-d');
        }
        if (!$this->validateDate($reportsDate)) {
            $inout->error('Wrong date format; Please used date format: yyyy-mm-dd.');
            return 0;
        }

        $output->writeln([
            'Mail Reports',
            '============',
            "date: ".$reportsDate,
            '',
        ]);
        $result ="";
        $result = $this->reportMailer->deliverReports(
            $reportsDate,
            $output
        );
        $output->writeln($result);
        return 1;
    }

    protected function validateDate($dateString, $format = 'Y-m-d')
    {
        $date = \DateTime::createFromFormat($format, $dateString);
        return $date && $date->format($format) === $dateString;
    }
}
