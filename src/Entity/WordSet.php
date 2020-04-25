<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WordSetRepository")
 */
class WordSet
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="wordSet")
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Word", mappedBy="wordSet", orphanRemoval=true,  cascade={"persist", "remove"})
     */
    private $words;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Origin", mappedBy="wordSets")
     */
    private $origins;


    public function __construct()
    {
        $this->words = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->origins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Word[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(Word $word): self
    {
        if (!$this->words->contains($word)) {
            $this->words[] = $word;
            $word->setWordSet($this);
        }

        return $this;
    }

    public function removeWord(Word $word): self
    {
        if ($this->words->contains($word)) {
            $this->words->removeElement($word);
            // set the owning side to null (unless already changed)
            if ($word->getWordSet() === $this) {
                $word->setWordSet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Origin[]
     */
    public function getOrigins(): Collection
    {
        return $this->origins;
    }

}
