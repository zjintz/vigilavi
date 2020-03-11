<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Controller\UserMailerController;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserChangedNotifier
{
    private $mailer;
    private $userMailer;
    
    public function __construct(
        \Swift_Mailer $mailer,
        UserMailerController $userMailer
    ) {
        $this->mailer = $mailer;
        $this->userMailer = $userMailer;        
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('enabled')) {
            if ($event->getNewValue('enabled')) {
                $this->userMailer->notifyActivation($user->getId(),$this->mailer);
            }
        }
    }
}
