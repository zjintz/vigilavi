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
        $words = $report->getWordSet()->getWords();
        $outcomes = $report->getOutcomes();
        $view = new ViewByWord();
        foreach ($words as $word) {
            $wordStat = new WordStat();
            $wordStat->setWordText($word->getText());
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
                        $userStats = $this->addUserStat(
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

    protected function addUserStat($stats, $userName, $isDenied)
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
}
