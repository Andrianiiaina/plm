<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class ProjectVoter extends Voter
{
    public const VIEW = 'PROJECT_VIEW';
    public const EDIT = 'PROJECT_EDIT';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {return true;}

        /** @var Project $project */
        $project = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($project, $user),
            self::EDIT => $this->canEdit($project, $user),
            default => throw new \LogicException('This code should not be reached!')
        };

    }
    public function canView($project,$user): bool{
        //Todo: les membres aussi doivent pouvoir voir
        $user === $project->getResponsable();
        return true;
    }
    public function canEDIT($project,$user): bool{
        
        return $user === $project->getResponsable();
    }
}
