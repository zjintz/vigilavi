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
    public function testGetOutcomesBasic()
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

    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class when
     * words are found in the url.
     *
     */
    public function testGetOutcomesUrlClassified()
    {
        $report = new Report();
        $wordset = new WordSet();
        $skypeWord = new Word();
        $skypeWord->setText("skype");
        $wordset->addWord($skypeWord);
        $report->setWordSet($wordset);
        $urlsDomains = [["skype.com/23/fs/gxs", "msn.com", "Allowed"],
                        ["abc.net/431", "abc.net","Allowed"],
                        ["www.biz.to/844", "biz.to", "Allowed"]
        ];
        $entries = $this->makeLogEntries($urlsDomains);

        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, $entries);
        $this->assertEquals(3, count($report->getOutcomes()));
        $this->assertEquals($newReport->getTotalLogEntries(), 3);
        $this->assertEquals($newReport->getTotalWords(), 1);
        $this->assertEquals(1, $report->getTotalClassifiedLogEntries());
        $this->assertEquals(3, $report->getTotalAllowedLogEntries());
        $this->assertEquals($report->getTotalDeniedLogEntries(), 0);
        $this->assertEquals($report->getTotalAllowedClassifiedLogEntries(), 1);
        $this->assertEquals(0, $report->getTotalDeniedClassifiedLogEntries());
        $this->assertEquals("URL", $report->getOutcomes()[0]->getClassification());
    }

    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class when
     * words are found in the Domain.
     *
     */
    public function testGetOutcomesDomainClassified()
    {
        $report = new Report();
        $wordset = new WordSet();
        $keyWord = new Word();
        $keyWord->setText("key");
        $wordset->addWord($keyWord);
        $report->setWordSet($wordset);
        $urlsDomains = [["skype.com/23/fs/gxs", "msn.com", "Allowed"],
                        ["abc.net/431", "key.net","Denied"],
                        ["www.biz.to/844", "biz.to", "Allowed"],
                        ["www.biz.net/", "biz.to", "Denied"]
        ];
        $entries = $this->makeLogEntries($urlsDomains);

        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, $entries);
        $this->assertEquals(4, count($report->getOutcomes()));
        $this->assertEquals(4, $newReport->getTotalLogEntries());
        $this->assertEquals(1, $newReport->getTotalWords());
        $this->assertEquals(1, $report->getTotalClassifiedLogEntries());
        $this->assertEquals(2, $report->getTotalAllowedLogEntries());
        $this->assertEquals(2, $report->getTotalDeniedLogEntries());
        $this->assertEquals(0, $report->getTotalAllowedClassifiedLogEntries());
        $this->assertEquals(1, $report->getTotalDeniedClassifiedLogEntries());
        $this->assertEquals("DOMAIN", $report->getOutcomes()[1]->getClassification());
    }

    /**
     * Tests the genReportOutcomes function from the ReportAnalizerAux class when
     * several words are found in the entry. (be it in the url, domain or both).
     *
     */
    public function testGetOutcomesNWords()
    {
        $report = new Report();
        $wordset = new WordSet();
        $keyWord = new Word();
        $keyWord->setText("key");
        $sinWord = new Word();
        $sinWord->setText("sin");
        $wordset->addWord($keyWord);
        $wordset->addWord($sinWord);
        $report->setWordSet($wordset);
        $urlsDomains = [["skeype.com/23/fs/gxs", "msn.sin", "Allowed"],
                        ["abc.net/431", "key.net","Denied"],
                        ["www.biz.to/844", "biz.to", "Allowed"],
                        ["www.bizkeytosin.net/", "biz.tosin", "Denied"]
        ];
        $entries = $this->makeLogEntries($urlsDomains);

        $reportAnalizerAux = new ReportAnalizerAux();
        $newReport = $reportAnalizerAux->genReportOutcomes($report, $entries);
        $this->assertEquals(4, count($report->getOutcomes()));
        $this->assertEquals(4, $newReport->getTotalLogEntries());
        $this->assertEquals(2, $newReport->getTotalWords());
        $this->assertEquals(3, $report->getTotalClassifiedLogEntries());
        $this->assertEquals(2, $report->getTotalAllowedLogEntries());
        $this->assertEquals(2, $report->getTotalDeniedLogEntries());
        $this->assertEquals(1, $report->getTotalAllowedClassifiedLogEntries());
        $this->assertEquals(2, $report->getTotalDeniedClassifiedLogEntries());
        $this->assertEquals("key sin", $report->getOutcomes()[0]->getWordsFound());
        $this->assertEquals("URL ; DOMAIN", $report->getOutcomes()[0]->getClassification());
        $this->assertEquals("URL ; URL&DOMAIN", $report->getOutcomes()[3]->getClassification());
    }

    protected function makeLogEntries($logData)
    {
        $logEntries = [];
        foreach ($logData as $entry) {
            $logEntry = new LogEntry();
            $logEntry->setUrl($entry[0]);
            $logEntry->setDomain($entry[1]);
            $logEntry->setLogSubType($entry[2]);
            $logEntries[] = $logEntry;
        }
        return $logEntries;
            
    }
    protected function assert0Stats($report)
    {
        $this->assertEquals(0, $report->getTotalClassifiedLogEntries());
        $this->assertEquals(0, $report->getTotalAllowedClassifiedLogEntries());
        $this->assertEquals(0, $report->getTotalDeniedClassifiedLogEntries());
    }
}
