<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ViewByWordRepository")
 */
class ViewByWord
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Report", inversedBy="viewByWord", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $report;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WordStat", mappedBy="viewByWord", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $wordStats;

    public function __construct()
    {
        $this->wordStats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @return Collection|WordStat[]
     */
    public function getWordStats(): Collection
    {
        return $this->wordStats;
    }


    public function getWordStatByWord(Word $word): ?WordStat
    {
        foreach ($this->wordStats as $wordStat) {
            if($wordStat->getWord()->getText() === $word->getText() ) {
                return $wordStat;
            }
        }
        return null;
    }

    public function addWordStat(WordStat $wordStat): self
    {
        if (!$this->wordStats->contains($wordStat)) {
            $this->wordStats[] = $wordStat;
            $wordStat->setViewByWord($this);
        }

        return $this;
    }

    public function removeWordStat(WordStat $wordStat): self
    {
        if ($this->wordStats->contains($wordStat)) {
            $this->wordStats->removeElement($wordStat);
            // set the owning side to null (unless already changed)
            if ($wordStat->getViewByWord() === $this) {
                $wordStat->setViewByWord(null);
            }
        }

        return $this;
    }
}
