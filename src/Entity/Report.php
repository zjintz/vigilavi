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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WordSet", inversedBy="reports")
     */
    private $wordSets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outcome", mappedBy="report", orphanRemoval=true)
     */
    private $outcomes;

    public function __construct()
    {
        $this->wordSets = new ArrayCollection();
        $this->outcomes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|WordSet[]
     */
    public function getWordSets(): Collection
    {
        return $this->wordSets;
    }

    public function addWordSet(WordSet $wordSet): self
    {
        if (!$this->wordSets->contains($wordSet)) {
            $this->wordSets[] = $wordSet;
        }

        return $this;
    }

    public function removeWordSet(WordSet $wordSet): self
    {
        if ($this->wordSets->contains($wordSet)) {
            $this->wordSets->removeElement($wordSet);
        }

        return $this;
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
}
