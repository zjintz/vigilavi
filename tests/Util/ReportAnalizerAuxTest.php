<?php
namespace App\Tests\Util;

use App\Entity\LogEntry;
use App\Entity\Outcome;
use App\Entity\Report;
use App\Entity\Word;
use App\Entity\WordSet;
use App\Util\ReportAnalizerAux;
use PHPUnit\Framework\TestCase;

class ReportAnalizerAuxTest extends TestCase
{
    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class, when
     * there are no entries.
     *
     */
    public function testGetOutcomesNoEntries()
    {
        $report = new Report();
        $wordset = new WordSet();
        $report->setWordSet($wordset);
        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, []);
        $this->assert0Stats($newReport);
        $this->assertEquals(0, count($report->getOutcomes()));
        $this->assertEquals(0, $newReport->getTotalLogEntries());

    }

    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class, when
     * there are no words.
     *
     */
    public function testGetOutcomesNoWords()
    {
        $report = new Report();
        $wordset = new WordSet();
        $report->setWordSet($wordset);
        $logEntry = new LogEntry();
        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, [$logEntry]);
        $this->assert0Stats($newReport);
        $this->assertEquals(1, count($report->getOutcomes()));
        $this->assertEquals(1, $newReport->getTotalLogEntries());
        $this->assertEquals(0, $newReport->getTotalWords());
    }
    
    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class.
     *
     */
    public function testGetOutcomes()
    {
        $report = new Report();
        $wordset = new WordSet();
        $skypeWord = new Word();
        $skypeWord->setText("skype");
        $wordset->addWord($skypeWord);
        $report->setWordSet($wordset);
        $logEntry = new LogEntry();
        $logEntry->setLogSubType("Allowed");
        $logEntry->setUrl("abc");
        $logEntry->setDomain("abc");
        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, [$logEntry]);
        $this->assertEquals(1, count($report->getOutcomes()));
        $this->assertEquals($newReport->getTotalLogEntries(), 1);
        $this->assertEquals($newReport->getTotalWords(), 1);
        $this->assertEquals($report->getTotalClassifiedLogEntries(), 0);
        $this->assertEquals($report->getTotalAllowedLogEntries(), 1);
        $this->assertEquals($report->getTotalDeniedLogEntries(), 0);
        $this->assertEquals($report->getTotalAllowedClassifiedLogEntries(), 0);
        $this->assertEquals($report->getTotalDeniedClassifiedLogEntries(), 0);
    }

    protected function assert0Stats($report)
    {
        $this->assertEquals(0, $report->getTotalClassifiedLogEntries());
        $this->assertEquals(0, $report->getTotalAllowedClassifiedLogEntries());
        $this->assertEquals(0, $report->getTotalDeniedClassifiedLogEntries());
    }
}
