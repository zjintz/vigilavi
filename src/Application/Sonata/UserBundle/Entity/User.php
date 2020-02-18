<?php

namespace App\Application\Sonata\UserBundle\Entity;

use App\Entity\EmailSubscription;
use App\Entity\Headquarter;
use App\Entity\Origin;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser
{

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Headquarter", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="App\Entity\Headquarter")
     * @Assert\Valid
     */
    private $headquarter;

    
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\EmailSubscription", inversedBy="user", cascade={"persist", "remove"})
     */
    private $emailSubscription;

    /**
     * Many Users have Many Origins.
     * @ORM\ManyToMany(targetEntity="App\Entity\Origin")
     * @ORM\JoinTable(name="users_origins",
     *      joinColumns={@ORM\JoinColumn(name="origin_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $origins;

    public function __construct()
    {
        $this->origins = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getHeadquarter(): ?Headquarter
    {
        return $this->headquarter;
    }

    public function setHeadquarter(?Headquarter $headquarter): self
    {
        $this->headquarter = $headquarter;

        return $this;
    }

     /**
     * Overridden so that username is now optional
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);
        return parent::setEmail($email);
    }

    public function getEmailSubscription(): ?EmailSubscription
    {
        return $this->emailSubscription;
    }

    public function setEmailSubscription(EmailSubscription $emailSubscription): self
    {
        $this->emailSubscription = $emailSubscription;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getOrigins(): Collection
    {
        return $this->origins;
    }

    public function addOrigin(Origin $origin): self
    {
        if (!$this->origins->contains($origin)) {
            $this->origins[] = $origin;
        }
        return $this;
    }

    public function removeOrigin(Origin $origin): self
    {
        if ($this->origins->contains($origin)) {
            $this->origins->removeElement($origin);
        }
    }

}
