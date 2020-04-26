<?php

namespace App\Report;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\UserStat;
use App\Entity\ViewByWord;
use App\Entity\Word;
use App\Entity\WordStat;

/**
 * \brief     A service that makes views of reports by words.
 *
 *
 */
class ViewByWordMaker
{
    /**
     * Makes the view by word of a given Report.
     *
     *
     */
    public function makeView(Report $report): ViewByWord
    {
        $words = $this->getAllWords($report->getOrigin()->getWordsets());
        $outcomes = $report->getOutcomes();
        $view = new ViewByWord();
        foreach ($words as $word) {
            if ($this->isWordDone($word, $view->getWordStats())) {
                
                continue;
            }
            $wordStat = $this->initWordStat($word);
            $view->addWordStat($wordStat);
            $deniedCount = 0;
            $userStats = [];
            foreach ($outcomes as $outcome) {
                if ($this->isOutcomeClassified($outcome)) {
     
                    if ( $this->isWordInOutcome(
                        $word->getText(),
                        $outcome->getWordsFound()
                    )
                    ) {
                        $wordStat->addOutcome($outcome);
                        $logSubType = $outcome->getLogEntry()->getLogSubType();
                        $userName =  $outcome->getLogEntry()->getUserName();
                        $isDenied = $logSubType === "Denied";
                        if( $isDenied ) {
                            $deniedCount += 1;
                        }
                        $userStats = $this->updateUserStats(
                            $userStats,
                            $userName,
                            $isDenied
                        );
                    }
                }
            }
            $allowedCount = count($wordStat->getOutcomes()) - $deniedCount;
            $wordStat->setDeniedEntries($deniedCount);
            $wordStat->setAllowedEntries($allowedCount);
            foreach($userStats as $userStat) {
                $wordStat->addUserStat($userStat);
            }
        }
        return $view;
    }

    /**
     * Given an array of UserStat this function push a new UserStat for an
     * user if is not in the array. If is in the array it updates the count of 
     * denied or allowed entries for that user.
     *
     */
    protected function updateUserStats(array $stats, string $userName, bool $isDenied)
    {
        foreach($stats as $userStat) {
            if ($userStat->getName() === $userName) {
                if ($isDenied) {
                    $deniedCount = $userStat->getDeniedEntries();
                    $userStat->setDeniedEntries($deniedCount + 1);
                    return $stats;
                }
                $allowedCount = $userStat->getAllowedEntries();
                $userStat->setAllowedEntries($allowedCount +1);
                return $stats;
            }
        }
        $userStat = new UserStat();
        $userStat->setName($userName);
        if ($isDenied) {
            $userStat->setDeniedEntries(1);
            $userStat->setAllowedEntries(0);
            $stats[] = $userStat;
            return $stats;
        }
        $userStat->setDeniedEntries(0);
        $userStat->setAllowedEntries(1);
        $stats[] = $userStat;
        return $stats;
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

    protected function getAllWords($wordsets)
    {
        $words = [];
        foreach ($wordsets as $set) {
            $words = array_merge($words, $set->getWords()->toArray());
        }
        return $words;
    }

    protected function initWordStat($word)
    {
        $wordStat = new WordStat();
        $wordStat->setWordText($word->getText());
        $wordStat->setWordSetsNames($word->getWordSet()->getName());
        return $wordStat;
    }

    /** Checks if the word is already done in the wordstats of this view.
     *
     * If is already done it adds the name of the wordSet to the wordSetsNames
     * of the stat.
    */
    protected function isWordDone($word , $wordStats)
    {
        foreach ($wordStats as $stat) {
            if ($word->getText() === $stat->getWordText()) {
                $stat->setWordSetsNames(
                    $stat->getWordSetsNames()."; ".$word->getWordSet()->getName()
                );
                return true; 
            }
        }
        return false;
    }        
}
