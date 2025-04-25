<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const APPROVE = 'APPROVE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::APPROVE]) && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Admin can everything
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Author can edit/delete their own comment
        if (in_array($attribute, [self::EDIT, self::DELETE])) {
            return $comment->getAuthor() && $comment->getAuthor()->getId() === $user->getId();
        }

        // Approve: only admin can approve
        if ($attribute === self::APPROVE) {
            return false;
        }

        return false;
    }
}
