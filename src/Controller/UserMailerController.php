<?php

namespace App\Controller;

use App\Application\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserMailerController extends AbstractController
{

    /**
     * Notifies an user when the account is activated.
     *
     * @Route("/notify/{userId}/activation",
     * name="notify_activation")
     *
     */
    public function notifyActivation($userId, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userRepo = $entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['id'=>$userId]);
        $name = $user->getFirstname();
        if (is_null($name)) {
            $name = "novo usuario";
        }
        $email = $user->getEmail();
        $message = (new \Swift_Message('Sua conta foi ativada'));
        $message->setFrom('no_reply@vigilavi.org');
        $message->setTo($email);
        $message->setBody(
                     $this->render(
                         'emails/account_enabled.html.twig',
                         ['name' => $name]
                     ),
                     'text/html'
                 )
                 ->addPart(
                     $this->render(
                         'emails/account_enabled.txt.twig',
                         ['name' => $name]
                     ),
                     'text/plain'
                 );
        $mailer->send($message);
        $response = new Response('done.', Response::HTTP_OK);
        return $response;
    }
    
    /**
     * Notifies the admin users that a new user registered to vigilavi.
     *
     * @Route("/notify/new_user/",
     * name="notify_new_user")
     *
     */
    public function notifyNewUserToAdmins(\Swift_Mailer $mailer)
    {
        $adminUsers = $this->getAdmins();
        foreach ($adminUsers as $admin) {
            $message = (new \Swift_Message('Novo Usuario do vigilavi.org'))
                     ->setFrom('no_reply@vigilavi.org')
                     ->setTo($admin->getEmail())
                     ->setBody(
                         $this->render(
                             'emails/new_user_notify.html.twig'
                         ),
                         'text/html'
                     )
                     ->addPart(
                         $this->render(
                             'emails/new_user_notify.txt.twig'
                         ),
                         'text/plain'
                     );
            $mailer->send($message);
        }
        $response = new Response('done.', Response::HTTP_OK);
        return $response;
    }

    protected function getAdmins()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userRepo = $entityManager->getRepository(User::class);
        $enabledUsers = $userRepo->findBy(['enabled'=>true]);
        $adminUsers =[];
        foreach ($enabledUsers as $user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $adminUsers[] = $user;
            }
        }
        return $adminUsers;
    }
}
