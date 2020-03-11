<?php

namespace App\Report;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Origin;
use App\Entity\Report;
use App\Entity\WordSet;
use Doctrine\ORM\EntityManagerInterface;

/**
 * \brief     Mails the Report texts.
 *
 *
 */
class ReportMailerAttacher
{
    private $entityManager;
    private $templating;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        \Twig_Environment $templating
    ) {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function makeAllViews($date, $dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $wordsets = $this->entityManager->getRepository(WordSet::class)->findAll();
        if (!$wordsets) {
            return [];
        }
        $origins = $this->entityManager->getRepository(Origin::class)->findAll();
        if (!$origins) {
            return [];
        }
        $entryDate = \DateTime::createFromFormat('yy-m-d', $date);
        $reports = $this->entityManager->getRepository(Report::class)
                                       ->findBy(["date"=>$entryDate]);
        $paths = [];
        foreach ($wordsets as $wordset) {
            foreach ($origins as $origin) {
                foreach ($reports as $report) {
                    $paths[] = $this->makeView(
                        $entryDate,
                        $dir,
                        $wordset,
                        $origin,
                        $report
                    );
                }
            }
        }
        return $paths;
    }

    protected function makeView($entryDate, $dir, $wordset, $origin,  $report)
    {
        // Provide a name for your file with extension
        $dateStr = $entryDate->format("Y-m-d");
        $filename = $this->makeFileName($wordset, $origin, $dateStr, $dir);
        // The dinamically created content of the file
        $fileContent = $this->templating->render(
            "report/by-words.html.twig",
            array('report'=> $report)
        );
        file_put_contents($filename, $fileContent);
        return $filename;
    } 
    
    public function listAttachments(User $user, string $date, string $dir=null)
    {
        $wordsets = $this->entityManager->getRepository(WordSet::class)->findAll();
        if (empty($wordsets)) {
            return [];            
        }
        $origins = $user->getOrigins();
        //isEmpty is a doctrine colletion method!
        if ($origins->isEmpty()) {
            return [];            
        }
        $fileList = [];
        foreach ($wordsets as $wordset) {
            foreach ($origins as $origin){
                $fileList[] = $this->makeFileName($wordset, $origin, $date, $dir);
            }
        }
        return $fileList;
    }

    protected function makeFileName($wordset, $origin, $date, $dir)
    {
        if ($dir === null) {
            return "wv_".$date."_".$wordset->getName()."_".$origin->getName().".html";
        }
        if(substr($dir, -1) !== "/") {
            $dir = $dir."/";
        }
        return $dir."wv_".$date."_".$wordset->getName()."_".$origin->getName().".html";
    }    
}
