<?php

namespace App\Util;

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
class ReportAnalizerAux
{
    public function genReportOutcomes(Report $report, array $entries) : Report
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

    public function genViewByWord(Report $report): ViewByWord
    {
        $words = $report->getWordSet()->getWords();
        $outcomes = $report->getOutcomes();
        $view = new ViewByWord();
        foreach ($words as $word) {
            $wordStat = new WordStat();
            $wordStat->setWord($word);
            $view->addWordStat($wordStat);
            foreach ($outcomes as $outcome) {
                if ($this->isOutcomeClassified($outcome)) {
     
                    if (
                        $this->isWordInOutcome(
                            $word->getText(),
                            $outcome->getWordsFound()
                        )
                    ) {
                        $wordStat->addOutcome($outcome);
                    }
                }
            }
        }
        return $view;
    }

    protected function isOutcomeClassified($outcome)
    {
        $classification = $outcome->getClassification();
        if ($classification !== "") {
            return true;
        }
        return false;
    }
    protected function isWordInOutcome(string $word, ?string $wordsFound)
    {
        if($word === $wordsFound) {
            return true;
        }
        $wordsFound = explode(" ", $wordsFound);
        foreach ($wordsFound as $found) {
            if ($word === $found) {
                return true;
            }
        }
        return false;
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
