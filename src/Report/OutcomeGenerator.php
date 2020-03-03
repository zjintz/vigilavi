<?php

namespace App\Report;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\Word;
use App\Entity\ViewByWord;
use App\Entity\WordStat;

/**
 * \brief     A service that helps to build the data of the reports. 
 *
 *
 */
class OutcomeGenerator
{
    /**
     * Generates all the outcomes of a report given a set of entries.
     *
     * \returns The report modified with the outcomes. 
     */
    public function genOutcomes(Report $report, array $entries) : Report
    {
        $words = $report->getWordSet()->getWords();
        $report->setTotalWords(count($words));
        $report->setTotalLogEntries(count($entries));
        $totalAllowedEntries =0;
        $totalDeniedEntries = 0;
        $classifiedEntries = 0;
        $allowedClassifiedEntries = 0;
        $deniedClassifiedEntries = 0;
        foreach ($entries as $entry) {
            if ($entry->getLogSubType() === "Allowed") {
                $totalAllowedEntries +=1;
            }
            
            if ($entry->getLogSubType() === "Denied") {
                $totalDeniedEntries +=1;
            }
            
            $classes = '';
            $wordsFound = '';
            $classified = false;
            
            foreach ($words as $word) {
                $class = $this->classifyWord($entry , $word);
                if ($class !== "ok") {
                    $classified = true;
                    if ( $wordsFound !== '') {
                        $wordsFound = $wordsFound." ";
                        $classes = $classes." ; ";
                    }
                    $wordsFound = $wordsFound.$word->getText();
                    $classes = $classes.$class;
                }
            }
            
            if ($classified) {
                $classifiedEntries +=1;
                if ($entry->getLogSubType() === "Allowed") {
                    $allowedClassifiedEntries +=1;
                }
                if ($entry->getLogSubType() === "Denied") {
                    $deniedClassifiedEntries +=1;
                }
            }
            $newOutcome = $this->makeOutcome($classes, $entry, $wordsFound);
            $report->addOutcome($newOutcome);
        }
        $report->setTotalAllowedLogEntries($totalAllowedEntries);
        $report->setTotalDeniedLogEntries($totalDeniedEntries);
        $report->setTotalClassifiedLogEntries($classifiedEntries);
        $report->setTotalAllowedClassifiedLogEntries($allowedClassifiedEntries);
        $report->setTotalDeniedClassifiedLogEntries($deniedClassifiedEntries);

        return $report;
    }
    
    protected function makeOutcome($classes, $entry, $wordsFound)
    {
        $newOutcome = new Outcome();
        $newOutcome->setClassification($classes);
        $newOutcome->setLogEntry($entry);
        $newOutcome->setWordsFound($wordsFound);
        return $newOutcome;
    }

    protected function classifyWord(LogEntry $entry, Word $word)
    {
        $isInUrl = strPos($entry->getUrl(), $word->getText()) !== false;
        $isInDomain = strPos($entry->getDomain(), $word->getText()) !== false;
        if ($isInUrl && $isInDomain) {
            return "URL&DOMAIN";
        }
        if ($isInUrl) {
            return "URL";
        }
        if ($isInDomain) {
            return "DOMAIN";
        }
        return "ok";
    }
}
