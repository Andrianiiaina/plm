<?php

namespace App\Security\Voter;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectVoter extends Voter
{
    //ReadUpdatetDelete
    public const OPERATE = 'operation';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support and subject isn't `Post` objects, return false
        return in_array($attribute, [self::OPERATE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {return true;}
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) { return false; }
        return $user === $subject->getResponsableId();
     
    }
}
