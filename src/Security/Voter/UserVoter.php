<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const POST = 'USER_POST';
    public const DELETE = 'USER_DELETE';
    public const UPDATE = 'USER_UPDATE';
    public const USER_VIEW_ALL = 'USER_VIEW_ALL';

    private ?Security $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::POST, self::UPDATE, self::USER_VIEW_ALL])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if (($this->security->isGranted('ROLE_USER') && $subject == $user) or
                    $this->security->isGranted('ROLE_ADMIN') or
                    ($this->security->isGranted('ROLE_USER') and $subject == $user and $subject->getRoles() == ['ROLE_HOST'])) {
                    return true;
                }
                break;
            case self::USER_VIEW_ALL:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            case self::VIEW:
                if ($this->security->isGranted('ROLE_ADMIN') or $subject == $user) {
                    return true;
                }
                break;
            case self::UPDATE:
                // logic to determine if the user can Update
                // return true or false
                return true;
                break;
            case self::POST:
                return false;
                break;
            case self::DELETE:
                return true;
                break;

        }

        return false;
    }
}
