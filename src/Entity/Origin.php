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
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="origin", orphanRemoval=true)
     */
    private $reports;


    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $subnet;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogEntry", mappedBy="origin")
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

    
    public function __construct()
    {
        $this->reports = new ArrayCollection();
        $this->logEntries = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setOrigin($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getOrigin() === $this) {
                $report->setOrigin(null);
            }
        }

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

    /**
     * @return Collection|LogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    public function addLogEntry(LogEntry $logEntry): self
    {
        if (!$this->logEntries->contains($logEntry)) {
            $this->logEntries[] = $logEntry;
            $logEntry->setOrigin($this);
        }

        return $this;
    }

    public function removeLogEntry(LogEntry $logEntry): self
    {
        if ($this->logEntries->contains($logEntry)) {
            $this->logEntries->removeElement($logEntry);
            // set the owning side to null (unless already changed)
            if ($logEntry->getOrigin() === $this) {
                $logEntry->setOrigin(null);
            }
        }

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

    /**
     * @return Collection|Origin[]
     */
    public function getUsers(): Collection
    {
        return $this->origins;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
    }
}
