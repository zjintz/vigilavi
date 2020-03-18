<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogEntryRepository")
 */
class LogEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_type;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_subtype;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $user_name;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $src_ip;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $dst_ip;

    /**
     * @ORM\Column(type="text")
     */
    private $domain;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outcome", mappedBy="logEntry", orphanRemoval=true)
     */
    private $outcomes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Origin", inversedBy="logEntries")
     */
    private $origin;

    /**
     * @ORM\Column(type="datetime")
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

    public function getLogType(): ?string
    {
        return $this->log_type;
    }

    public function setLogType(string $log_type): self
    {
        $this->log_type = $log_type;

        return $this;
    }

    public function getLogSubtype(): ?string
    {
        return $this->log_subtype;
    }

    public function setLogSubtype(string $log_subtype): self
    {
        $this->log_subtype = $log_subtype;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(?string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getSrcIp(): ?string
    {
        return $this->src_ip;
    }

    public function setSrcIp(string $src_ip): self
    {
        $this->src_ip = $src_ip;

        return $this;
    }

    public function getDstIp(): ?string
    {
        return $this->dst_ip;
    }

    public function setDstIp(string $dst_ip): self
    {
        $this->dst_ip = $dst_ip;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }


    /**
     * @return Collection|Outcome[]
     */
    public function getOutcomes(): Collection
    {
        return $this->outcomes;
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
