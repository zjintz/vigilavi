<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Twig\Environment;

class UserChangedNotifier
{
    private $mailer;
    private $twig;
    
    public function __construct(Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('enabled')) {
            if ($event->getNewValue('enabled')) {
                 $message = (new \Swift_Message('Sua conta foi ativada'))
                          ->setFrom('no_reply@liturgiacheznous.org')
                          ->setTo($user->getEmail())
                          ->setBody(
                              $this->twig->render(
                                  'emails/account_enabled.html.twig',
                                  ['name' => $user->getFirstname()]
                              ),
                              'text/html'
                          )
                          ->addPart(
                              $this->twig->render(
                                  'emails/account_enabled.txt.twig',
                                  ['name' => $user->getFirstname()]
                              ),
                              'text/plain'
                          );
                 $this->mailer->send($message);
            }
        }
    }
}
