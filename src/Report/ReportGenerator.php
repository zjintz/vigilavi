<?php

namespace App\Report;

use App\Entity\Origin;
use App\Entity\Report;
use App\Entity\WordSet;
use Doctrine\ORM\EntityManagerInterface;


/**
 * \brief     A service to make reports.
 *
 *
 */
class ReportGenerator
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Generates all the reports for a given date.
     *
     * \returns The total number of generated reports. 
     */
    public function generateAllReports(string $reportsDate = null) : array
    {
        $date = \DateTime::createFromFormat('yy-m-d', $reportsDate);

        if (!$date && !is_null($reportsDate)) {
            return ['error' => 'Date format not supported, the format should be yy-m-d.'];
        }
        if (is_null($reportsDate))
        {
            $today = new \DateTime();
            $date = $today->sub(new \DateInterval('P1D'));
        }
        $origins = $this->entityManager->getRepository(Origin::class)->findAll();
        $wordsets = $this->entityManager->getRepository(WordSet::class)->findAll();
        $counter = 0;
        foreach ($origins as $origin) {
            foreach ($wordsets as $wordset) {
                $newReport = new Report();
                $newReport->setDate($date);
                $newReport->setWordSet($wordset);
                $newReport->setOrigin($origin);
                $counter += 1;
                $this->entityManager->persist($newReport);
            }
        }
        $this->entityManager->flush();
        return ['total'=> $counter];

    }
    
}
