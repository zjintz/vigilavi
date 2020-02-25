<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outcome", mappedBy="report", orphanRemoval=true, cascade={"persist"})
     */
    private $outcomes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WordSet", inversedBy="reports", cascade={"persist"})
     */
    private $wordSet;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Origin", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $origin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalWords;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalLogEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalAllowedLogEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalDeniedLogEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalClassifiedLogEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalAllowedClassifiedLogEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalDeniedClassifiedLogEntries;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ViewByWord", mappedBy="report", cascade={"persist", "remove"})
     */
    private $viewByWord;

    public function __construct()
    {
        $this->outcomes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Outcome[]
     */
    public function getOutcomes(): Collection
    {
        return $this->outcomes;
    }

    public function addOutcome(Outcome $outcome): self
    {
        if (!$this->outcomes->contains($outcome)) {
            $this->outcomes[] = $outcome;
            $outcome->setReport($this);
        }

        return $this;
    }

    public function removeOutcome(Outcome $outcome): self
    {
        if ($this->outcomes->contains($outcome)) {
            $this->outcomes->removeElement($outcome);
            // set the owning side to null (unless already changed)
            if ($outcome->getReport() === $this) {
                $outcome->setReport(null);
            }
        }

        return $this;
    }

    public function getWordSet(): ?WordSet
    {
        return $this->wordSet;
    }

    public function setWordSet(?WordSet $wordSet): self
    {
        $this->wordSet = $wordSet;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOrigin(): ?Origin
    {
        return $this->origin;
    }

    public function setOrigin(?Origin $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getTotalWords(): ?int
    {
        return $this->totalWords;
    }

    public function setTotalWords(?int $totalWords): self
    {
        $this->totalWords = $totalWords;

        return $this;
    }

    public function getTotalLogEntries(): ?int
    {
        return $this->totalLogEntries;
    }

    public function setTotalLogEntries(?int $totalLogEntries): self
    {
        $this->totalLogEntries = $totalLogEntries;

        return $this;
    }

    public function getTotalAllowedLogEntries(): ?int
    {
        return $this->totalAllowedLogEntries;
    }

    public function setTotalAllowedLogEntries(?int $totalAllowedLogEntries): self
    {
        $this->totalAllowedLogEntries = $totalAllowedLogEntries;

        return $this;
    }

    public function getTotalDeniedLogEntries(): ?int
    {
        return $this->totalDeniedLogEntries;
    }

    public function setTotalDeniedLogEntries(?int $totalDeniedLogEntries): self
    {
        $this->totalDeniedLogEntries = $totalDeniedLogEntries;

        return $this;
    }

    public function getTotalClassifiedLogEntries(): ?int
    {
        return $this->totalClassifiedLogEntries;
    }

    public function setTotalClassifiedLogEntries(?int $totalClassifiedLogEntries): self
    {
        $this->totalClassifiedLogEntries = $totalClassifiedLogEntries;

        return $this;
    }

    public function getTotalAllowedClassifiedLogEntries(): ?int
    {
        return $this->totalAllowedClassifiedLogEntries;
    }

    public function setTotalAllowedClassifiedLogEntries(?int $totalAllowedClassifiedLogEntries): self
    {
        $this->totalAllowedClassifiedLogEntries = $totalAllowedClassifiedLogEntries;

        return $this;
    }

    public function getTotalDeniedClassifiedLogEntries(): ?int
    {
        return $this->totalDeniedClassifiedLogEntries;
    }

    public function setTotalDeniedClassifiedLogEntries(?int $totalDeniedClassifiedLogEntries): self
    {
        $this->totalDeniedClassifiedLogEntries = $totalDeniedClassifiedLogEntries;

        return $this;
    }

    public function getViewByWord(): ?ViewByWord
    {
        return $this->viewByWord;
    }

    public function setViewByWord(ViewByWord $viewByWord): self
    {
        $this->viewByWord = $viewByWord;

        // set the owning side of the relation if necessary
        if ($viewByWord->getReport() !== $this) {
            $viewByWord->setReport($this);
        }

        return $this;
    }
}
