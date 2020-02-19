<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WordStatRepository")
 */
class WordStat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ViewByWord", inversedBy="wordStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $viewByWord;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outcome")
     */
    private $outcomes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Word")
     * @ORM\JoinColumn(nullable=false)
     */
    private $word;

    public function __construct()
    {
        $this->outcomes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getViewByWord(): ?ViewByWord
    {
        return $this->viewByWord;
    }

    public function setViewByWord(?ViewByWord $viewByWord): self
    {
        $this->viewByWord = $viewByWord;

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
        }

        return $this;
    }

    public function removeOutcome(Outcome $outcome): self
    {
        if ($this->outcomes->contains($outcome)) {
            $this->outcomes->removeElement($outcome);
        }

        return $this;
    }

    public function getWord(): ?Word
    {
        return $this->word;
    }

    public function setWord(?Word $word): self
    {
        $this->word = $word;

        return $this;
    }
}
