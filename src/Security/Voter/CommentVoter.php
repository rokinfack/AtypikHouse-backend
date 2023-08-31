<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Repository\ReservationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const POST = 'COMMENT_POST';
    public const VIEW = 'COMMENT_VIEW';

    private ReservationRepository $reservationRepository;
    private Security $security;

    public function __construct(ReservationRepository $reservationRepository, Security $security)
    {
        $this->reservationRepository = $reservationRepository;
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::POST])
            && $subject instanceof \App\Entity\Comment;
    }

    /**
     * @param string $attribute
     * @param TokenInterface $token
     * @param Comment $subject
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $reservation = $this->reservationRepository->findBy([
            "user" => $user->getId(),
            "habitat" => $subject->getHabitat()->getId()
        ], null, 1);

        $isReservation = (bool)count($reservation);

        var_dump($isReservation);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                if (($isReservation and $this->security->isGranted("ROLE_USER"))
                    or $this->security->isGranted("ROLE_ADMIN")) return true;
                break;
            case self::POST:
                if ($isReservation or $this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
