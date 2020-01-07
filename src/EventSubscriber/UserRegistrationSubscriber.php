<?php

namespace App\EventSubscriber;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\EmailSubscription;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class UserRegistrationSubscriber implements EventSubscriberInterface
{
    private $router;
    private $session;
    private $entityManager;
    private $mailer;
    private $twig;
    
    public function __construct(
        RouterInterface $router,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        Environment $twig
    ) {
        $this->router = $router;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
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
        $this->sendAdminNotification();
        $event->setResponse($response);
    }

    private function sendAdminNotification()
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $enabledUsers = $userRepo->findBy(['enabled'=>true]);
        $adminUsers =[];
        foreach ($enabledUsers as $user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $adminUsers[] = $user;
            }
        }
        foreach ($adminUsers as $admin) {
            $message = (new \Swift_Message('Novo Usuario do vigilavi.org'))
                          ->setFrom('no_reply@vigilavi.org')
                          ->setTo($admin->getEmail())
                          ->setBody(
                              $this->twig->render(
                                  'emails/new_user_notify.html.twig'
                              ),
                              'text/html'
                          )
                          ->addPart(
                              $this->twig->render(
                                  'emails/new_user_notify.txt.twig'
                              ),
                              'text/plain'
                          );
                 $this->mailer->send($message);
        }
    }
}
