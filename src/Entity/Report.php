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
     * @ORM\OneToMany(targetEntity="App\Entity\Outcome", mappedBy="report", orphanRemoval=true)
     */
    private $outcomes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WordSet", inversedBy="reports")
     */
    private $wordSet;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

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
}
