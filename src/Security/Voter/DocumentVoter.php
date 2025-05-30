<?php

namespace App\Security\Voter;

use App\Entity\Document;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class DocumentVoter extends Voter
{
    public const OPERATE = 'operation';
    public const VIEW_LISTS = 'view_lists';
    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [ self::OPERATE, self::VIEW_LISTS])
            && $subject instanceof \App\Entity\Document;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) { return false; }
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {return true;}
        
        /** @var Document $document */
        $document = $subject;
        return match($attribute) {
            self::OPERATE => $this->canOperate($document, $user),
            self::VIEW_LISTS => $this->canViewLists($document, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
       

        if ($user === $subject->getTender()->getResponsable()) return true;
        return $user === $subject->getResponsable();
    }
    private function canOperate(Document $subject, User $user): bool
    {
        if ($user === $subject->getTender()->getResponsable()) return true;
        return $user === $subject->getResponsable();
    }
    private function canViewLists(Document $subject, User $user): bool
    {
        return $user === $subject->getTender()->getResponsable();
    }

}
