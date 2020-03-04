<?php

namespace App\Security\Authorization\Voter;

use App\Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const VIEW = 'ROLE_SONATA_USER_ADMIN_USER_VIEW';
    const EDIT = 'ROLE_SONATA_USER_ADMIN_USER_EDIT';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }
        // only vote on User objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var User $targetUser */
        $targetUser = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($targetUser, $user);
            case self::EDIT:
                return $this->canEdit($targetUser, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $targetUser, User $user)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        // if they can edit, they can view
        return $this->canEdit($targetUser, $user);
        //return true;
    }

    private function canEdit(User $targetUser, User $user)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        return $user === $targetUser;
    }
}
