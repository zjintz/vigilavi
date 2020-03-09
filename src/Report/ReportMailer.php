<?php

namespace App\Report;

use App\Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * \brief     Mails the Report texts.
 *
 *
 */
class ReportMailer
{
    private $twig;
    private $mailer;
    private $textsDir;
    private $entityManager;
    
    public function __construct(
        Environment $twig,
        \Swift_Mailer $mailer,
        EntityManagerInterface $entityManager
    ) {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * This function delivers the mails to every subscriber
     * acoording to their subscription.
     *
     * This function is used by the MailReports command.
     *
     */
    public function deliverReports($reportsDate, $output)
    {
        //$this->mailerAssistant->logTextsDeliver($period);
        $enabledUsers = $this->getEnabledUsers();
         
        if (empty($enabledUsers)) {
            return 'There are no enabled users in the DB.';
        }
        $subscribedUsers = $this->getSubscribedUsers($enabledUsers);
        
        if (empty($subscribedUsers)) {
            return 'There are no users with active email subscriptions for the given date.';
        }
        
        $output->writeln('Sending Reports ...');

        foreach ($subscribedUsers as $subscriber) {
            $output->writeln(
                '        - Sending reports to '.$subscriber->getEmail()
            );
            //            $this->sendTexts($subscriber);
        }
        return 'Done.';
    }

    protected function getEnabledUsers()
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $enabledUsers = $userRepo->findBy(['enabled'=>true]);
        return $enabledUsers;
    }
    
    /**
     * Get the users that have an actived email subscription.
     *
     *
     */
    protected function getSubscribedUsers($enabledUsers)
    {
        $subscribedUsers = [];
        foreach ($enabledUsers as $user) {
            $subsc = $user->getEmailSubscription();
            if (!is_null($subsc)) {
                $isActive =$subsc->getIsActive();
                if ($isActive) {
                    $subscribedUsers[] = $user;
                }
            }
        }
        return $subscribedUsers;
    }
}
