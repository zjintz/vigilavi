<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WordRepository")
 */
class Word
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
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WordSet", mappedBy="words")
     */
    private $wordSets;

    public function __construct()
    {
        $this->wordSets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

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
            $wordSet->addWord($this);
        }

        return $this;
    }

    public function removeWordSet(WordSet $wordSet): self
    {
        if ($this->wordSets->contains($wordSet)) {
            $this->wordSets->removeElement($wordSet);
            $wordSet->removeWord($this);
        }

        return $this;
    }
}
