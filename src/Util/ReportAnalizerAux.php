<?php

namespace App\Util;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;

/**
 * \brief     A service that helps to build the data of the reports. 
 *
 *
 */
class ReportAnalizerAux
{
    public function genReportOutcomes(Report $report, array $entries) : Report
    {
        $wordSet = $report->getWordSet();
        $words = $wordSet->getWords();
        $report->setTotalWords(count($words));
        $report->setTotalLogEntries(count($entries));
        $totalAllowedEntries =0;
        $totalDeniedEntries = 0;
        $totalClassifiedEntries = 0;
        $totalAllowedClassifiedEntries = 0;
        $totalDeniedClassifiedEntries = 0;
        //        $viewByWord = $this->initViewByWord($words);
        //$report->setViewByWord($viewByWord);
        foreach ($entries as $entry) {
            if ($entry->getLogSubType() === "Allowed") {
                $totalAllowedEntries +=1;
            }
            
            if ($entry->getLogSubType() === "Denied") {
                $totalDeniedEntries +=1;
            }
            
            $newOutcome = new Outcome();
            $class = 'ok';
            $wordsFound = '';
            $classified = false;
            
            foreach ($words as $word) {
                if (strPos($entry->getUrl(), $word->getText()) !== false) {
                    $classified = true;
                    $wordsFound = $wordsFound." ".$word;
                    $class = "(!!!)";
                }
            }
            
            if ($classified) {
                $totalClassifiedEntries +=1;
                if ($entry->getLogSubType() === "Allowed") {
                    $totalAllowedClassifiedEntries +=1;
                }
                if ($entry->getLogSubType() === "Denied") {
                    $totalDeniedClassifiedEntries +=1;
                }
            }

            $newOutcome->setClassification($class);
            $newOutcome->setReport($report);
            $newOutcome->setLogEntry($entry);
            $newOutcome->setWordsFound($wordsFound);
            $report->addOutcome($newOutcome);
        }
        $report->setTotalAllowedLogEntries($totalAllowedEntries);
        $report->setTotalDeniedLogEntries($totalDeniedEntries);
        $report->setTotalClassifiedLogEntries($totalClassifiedEntries);
        $report->setTotalAllowedClassifiedLogEntries($totalAllowedClassifiedEntries);
        $report->setTotalDeniedClassifiedLogEntries($totalDeniedClassifiedEntries);

        return $report;
    }
}
