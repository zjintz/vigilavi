<?php

namespace App\Entity;

use App\Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginRepository")
 */
class Origin
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
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="origin", orphanRemoval=true, cascade={"persist"})
     */
    private $reports;


    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $subnet;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogEntry", mappedBy="origin", cascade={"persist"})
     */
    private $logEntries;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active;


    /**
     * Many Origins have Many Users.
     * @ORM\ManyToMany(targetEntity="App\Application\Sonata\UserBundle\Entity\User", mappedBy="origins")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WordSet", inversedBy="origins")
     */
    private $wordSets;

    
    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->logEntries = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->wordSets = new ArrayCollection();
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

    public function getSubnet(): ?string
    {
        return $this->subnet;
    }

    public function setSubnet(?string $subnet): self
    {
        $this->subnet = $subnet;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
    public function __toString()
    {
        return $this->name;
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
}
