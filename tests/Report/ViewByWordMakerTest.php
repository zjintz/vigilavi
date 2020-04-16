<?php
namespace App\Tests\Report;

use App\Entity\LogEntry;
use App\Entity\Origin;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\Word;
use App\Entity\WordSet;
use App\Report\ViewByWordMaker;
use PHPUnit\Framework\TestCase;

class ViewByWordMakerTest extends TestCase
{
    /**
     * Tests the makeView function from the ViewByWordMaker class, when
     * there are no outcomes and no words.
     *
     */
    public function testMakeViewNothing()
    {
        $report = new Report();
        $origin = new Origin();
        $wordset = new WordSet();
        $origin->addWordset($wordset);
        $report->setOrigin($origin);
        $viewMaker = new ViewByWordMaker();
        $newView = $viewMaker->makeView($report);
        $this->assertEquals(0, count($newView->getWordStats()));
    }

    /**
     * Tests the makeView function from the ViewByWordMaker class, when
     * there are no outcomes.
     *
     */
    public function testMakeViewNoOutcomes()
    {
        $report = new Report();
        $origin = new Origin();
        $wordset = $this->make2WordSet();
        $origin->addWordset($wordset);
        $report->setOrigin($origin);
        $outcomeGenerator = new ViewByWordMaker();
        $newView = $outcomeGenerator->makeView($report);
        $this->assertEquals(2, count($newView->getWordStats()));
        $this->assertEquals(0, count($newView->getWordStats()[0]->getOutcomes()));
        $this->assertEquals(0, count($newView->getWordStats()[1]->getOutcomes()));
        $this->assertEquals(
            "key",
            $newView->getWordStats()[0]->getWordText()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "sin",
            $newView->getWordStats()[1]->getWordText()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getAllowedEntries()
        );
        
    }

    /**
     * Tests the makeView function from the ViewByWordMaker class, when
     * there are only 'ok' outcomes.
     *
     */
    public function testMakeViewOkOutcomes()
    {
        $report = new Report();
        $origin = new Origin();
        $wordset = $this->make2WordSet();
        $origin->addWordset($wordset);
        $report->setOrigin($origin);
        $report= $this->addOkOutcomes($report);
        $outcomeGenerator = new ViewByWordMaker();
        $newView = $outcomeGenerator->makeView($report);
        $this->assertEquals(2, count($newView->getWordStats()));
        $this->assertEquals(0, count($newView->getWordStats()[0]->getOutcomes()));
        $this->assertEquals(0, count($newView->getWordStats()[1]->getOutcomes()));
        $this->assertEquals(
            "key",
            $newView->getWordStats()[0]->getWordText()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "sin",
            $newView->getWordStats()[1]->getWordText()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getAllowedEntries()
        );
    }

    /**
     * Tests the makeView function from the ViewByWordMaker class, when
     * in the outcome there is the word in the URL.
     *
     */
    public function testMakeViewUrlOutcomes()
    {
        $report = new Report();
        $origin = new Origin();
        $wordset = $this->make2WordSet();
        $origin->addWordset($wordset);
        $report->setOrigin($origin);
        $report= $this->addUrlOutcomes($report);
        $viewMaker = new ViewByWordMaker();
        $newView = $viewMaker->makeView($report);
        $this->assertEquals(2, count($newView->getWordStats()));
        $this->assertEquals(1, count($newView->getWordStats()[0]->getOutcomes()));
        $this->assertEquals(3, count($newView->getWordStats()[1]->getOutcomes()));
        $this->assertEquals(
            "key",
            $newView->getWordStats()[0]->getWordText()
        );
        $this->assertEquals(
            1,
            $newView->getWordStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "Tester",
            $newView->getWordStats()[0]->getUserStats()[0]->getName()
        );
        $this->assertEquals(
            1,
            $newView->getWordStats()[0]->getUserStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getUserStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "sin",
            $newView->getWordStats()[1]->getWordText()
        );
        $this->assertEquals(
            1,
            $newView->getWordStats()[1]->getDeniedEntries()
        );
        $this->assertEquals(
            2,
            $newView->getWordStats()[1]->getAllowedEntries()
        );
        $this->assertEquals(
            "Tester",
            $newView->getWordStats()[1]->getUserStats()[0]->getName()
        );
        $this->assertEquals(
            1,
            $newView->getWordStats()[1]->getUserStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            2,
            $newView->getWordStats()[1]->getUserStats()[0]->getAllowedEntries()
        );
    }

    /**
     * Tests the makeView function from the ViewByWordMaker class.
     *
     */
    public function testMakeView()
    {
        $report = new Report();
        $origin = new Origin();
        $wordset = $this->make2WordSet();
        $origin->addWordset($wordset);
        $report->setOrigin($origin);
        $report= $this->addOutcomes($report);
        $viewMaker = new ViewByWordMaker();
        $newView = $viewMaker->makeView($report);
        $this->assertEquals(2, count($newView->getWordStats()));
        $this->assertEquals(4, count($newView->getWordStats()[0]->getOutcomes()));
        $this->assertEquals(2, count($newView->getWordStats()[1]->getOutcomes()));
        $this->assertEquals(
            "key",
            $newView->getWordStats()[0]->getWordText()
        );
        $this->assertEquals(
            4,
            $newView->getWordStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "Tester_NOVO",
            $newView->getWordStats()[0]->getUserStats()[0]->getName()
        );
        $this->assertEquals(
            3,
            $newView->getWordStats()[0]->getUserStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getUserStats()[0]->getAllowedEntries()
        );
        $this->assertEquals(
            "Tester",
            $newView->getWordStats()[0]->getUserStats()[1]->getName()
        );
        $this->assertEquals(
            1,
            $newView->getWordStats()[0]->getUserStats()[1]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[0]->getUserStats()[1]->getAllowedEntries()
        );
        $this->assertEquals(
            "sin",
            $newView->getWordStats()[1]->getWordText()
        );
        $this->assertEquals(
            2,
            $newView->getWordStats()[1]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getAllowedEntries()
        );
        $this->assertEquals(
            "Tester_NOVO",
            $newView->getWordStats()[1]->getUserStats()[0]->getName()
        );
        $this->assertEquals(
            2,
            $newView->getWordStats()[1]->getUserStats()[0]->getDeniedEntries()
        );
        $this->assertEquals(
            0,
            $newView->getWordStats()[1]->getUserStats()[0]->getAllowedEntries()
        );
    }
    protected function addOkOutcomes($report)
    {
        $outcome = new Outcome();
        $outcome->setClassification('');
        $report->addOutcome($outcome);
        $report->addOutcome($outcome);
        $report->addOutcome($outcome);
        return $report;
    }

    protected function addUrlOutcomes($report)
    {
        $outcome1 = new Outcome();
        $outcome1->setClassification('URL');
        $outcome1->setWordsFound("sin");
        $logEntry1 = new LogEntry();
        $logEntry1->setLogSubType("Allowed");
        $logEntry1->setUserName("Tester");
        $outcome1->setLogEntry($logEntry1);
        $outcome2 = new Outcome();
        $outcome2->setClassification('URL');
        $outcome2->setWordsFound("sin");
        $logEntry2 = new LogEntry();
        $logEntry2->setLogSubType("Allowed");
        $logEntry2->setUserName("Tester");
        $outcome2->setLogEntry($logEntry2);
        $outcome3 = new Outcome();
        $outcome3->setClassification('URL - URL');
        $outcome3->setWordsFound("sin key");
        $logEntry3 = new LogEntry();
        $logEntry3->setLogSubType("Denied");
        $logEntry3->setUserName("Tester");
        $outcome3->setLogEntry($logEntry3);
        $report->addOutcome($outcome1);
        $report->addOutcome($outcome2);
        $report->addOutcome($outcome3);
        return $report;
    }

    protected function addOutcomes($report)
    {
        $outcome1 = new Outcome();
        $outcome1->setClassification('DOMAIN - DOMAIN');
        $logEntry1 = new LogEntry();
        $logEntry1->setLogSubType("Denied");
        $logEntry1->setUserName("Tester_NOVO");
        $outcome1->setLogEntry($logEntry1);
        $outcome1->setWordsFound("key sin");
        $outcome2 = new Outcome();
        $outcome2->setClassification('URL');
        $outcome2->setWordsFound("key");
        $logEntry2 = new LogEntry();
        $logEntry2->setLogSubType("Denied");
        $logEntry2->setUserName("Tester");
        $outcome2->setLogEntry($logEntry2);
        $outcome3 = new Outcome();
        $outcome3->setClassification('URL - URL');
        $outcome3->setWordsFound("sin key");
        $logEntry3 = new LogEntry();
        $logEntry3->setLogSubType("Denied");
        $logEntry3->setUserName("Tester_NOVO");
        $outcome3->setLogEntry($logEntry3);
        $outcome4 = new Outcome();
        $outcome4->setClassification('DOMAIN');
        $outcome4->setWordsFound("key");
        $logEntry4 = new LogEntry();
        $logEntry4->setLogSubType("Denied");
        $logEntry4->setUserName("Tester_NOVO");
        $outcome4->setLogEntry($logEntry4);
        $report->addOutcome($outcome1);
        $report->addOutcome($outcome2);
        $report->addOutcome($outcome3);
        $report->addOutcome($outcome4);
        return $report;
    }

    protected function make2WordSet()
    {
        $wordset = new WordSet();
        $keyWord = new Word();
        $keyWord->setText("key");
        $sinWord = new Word();
        $sinWord->setText("sin");
        $wordset->addWord($keyWord);
        $wordset->addWord($sinWord);
        return $wordset;
    }  
}
