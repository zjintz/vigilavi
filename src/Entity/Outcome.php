<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutcomeRepository")
 */
class Outcome
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $classification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="outcomes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LogEntry", inversedBy="outcomes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $logEntry;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassification(): ?string
    {
        return $this->classification;
    }

    public function setClassification(string $classification): self
    {
        $this->classification = $classification;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getLogEntry(): ?LogEntry
    {
        return $this->logEntry;
    }

    public function setLogEntry(?LogEntry $logEntry): self
    {
        $this->logEntry = $logEntry;

        return $this;
    }
    public function __toString()
    {
        $outString = $this->classification.' -- URL: ';
        $outString = $outString.$this->logEntry->getUrl(); 
        return  $outString;
    }
}
