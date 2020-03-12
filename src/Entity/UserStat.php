<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserStatRepository")
 */
class UserStat
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
     * @ORM\Column(type="integer")
     */
    private $deniedEntries;

    /**
     * @ORM\Column(type="integer")
     */
    private $allowedEntries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WordStat", inversedBy="userStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wordStat;

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

    public function getDeniedEntries(): ?int
    {
        return $this->deniedEntries;
    }

    public function setDeniedEntries(int $deniedEntries): self
    {
        $this->deniedEntries = $deniedEntries;

        return $this;
    }

    public function getAllowedEntries(): ?int
    {
        return $this->allowedEntries;
    }

    public function setAllowedEntries(int $allowedEntries): self
    {
        $this->allowedEntries = $allowedEntries;

        return $this;
    }


    public function setWordStat(?WordStat $wordStat): self
    {
        $this->wordStat = $wordStat;

        return $this;
    }
}
