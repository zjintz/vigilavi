<?php

namespace App\EventSubscriber;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\EmailSubscription;
use App\Controller\UserMailerController;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class UserRegistrationSubscriber implements EventSubscriberInterface
{
    private $router;
    private $session;
    private $userMailer;
    private $mailer;
    
    public function __construct(
        RouterInterface $router,
        SessionInterface $session,
        UserMailerController $userMailer,
        \Swift_Mailer $mailer
    ) {
        $this->router = $router;
        $this->session = $session;
        $this->userMailer = $userMailer;
        $this->mailer = $mailer;
    }
    
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            FOSUserEvents::REGISTRATION_INITIALIZE => [
                ['disableUser', 0],
            ],
            FOSUserEvents::REGISTRATION_SUCCESS => [
                ['registrationFlashMessage', 0],
            ],
        ];
    }

    public function disableUser(GetResponseUserEvent $event)
    {
        $subscription = new EmailSubscription();
        $subscription->setIsActive(true);
        $event->getUser()->setEnabled(false);
        $event->getUser()->setEmailSubscription($subscription);
    }

    public function registrationFlashMessage(FormEvent $event)
    {
        $url = $this->router->generate('fos_user_security_login');
        $response = new RedirectResponse($url);
        
        $this->session->getFlashBag()->add(
            'regg-success',
            'user_registration.success_message'
        );
        $this->session->getFlashBag()->add(
            'regg-success',
            'user_registration.activation_notice'
        );
        $this->userMailer->notifyNewUserToAdmins($this->mailer);
        $event->setResponse($response);
    }

}
