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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deniedEntries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allowedEntries;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wordText;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserStat", mappedBy="wordStat", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $userStats;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $wordSetsNames;

    public function __construct()
    {
        $this->outcomes = new ArrayCollection();
        $this->userStats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getDeniedEntries(): ?int
    {
        return $this->deniedEntries;
    }

    public function setDeniedEntries(?int $deniedEntries): self
    {
        $this->deniedEntries = $deniedEntries;

        return $this;
    }

    public function getAllowedEntries(): ?int
    {
        return $this->allowedEntries;
    }

    public function setAllowedEntries(?int $allowedEntries): self
    {
        $this->allowedEntries = $allowedEntries;

        return $this;
    }

    public function getWordText(): ?string
    {
        return $this->wordText;
    }

    public function setWordText(string $wordText): self
    {
        $this->wordText = $wordText;

        return $this;
    }

    /**
     * @return Collection|UserStat[]
     */
    public function getUserStats(): Collection
    {
        return $this->userStats;
    }

    public function addUserStat(UserStat $userStat): self
    {
        if (!$this->userStats->contains($userStat)) {
            $this->userStats[] = $userStat;
            $userStat->setWordStat($this);
        }

        return $this;
    }

    public function getWordSetsNames(): ?string
    {
        return $this->wordSetsNames;
    }

    public function setWordSetsNames(?string $wordSetsNames): self
    {
        $this->wordSetsNames = $wordSetsNames;

        return $this;
    }

}
