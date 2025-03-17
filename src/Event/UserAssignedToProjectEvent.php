<?php
    namespace App\Event;

    use App\Entity\User;
    use App\Entity\Project;
    use Symfony\Contracts\EventDispatcher\Event;

    class UserAssignedToProjectEvent extends Event
    {
        public function __construct(
            private User $user,
            private int $project,
            private string $type
        ) {}

        public function getUser(): User
        {
            return $this->user;
        }

        public function getProject(): int
        {
            return $this->project;
        }
        public function getType(): string
        {
            return $this->type;
        }
    }

?>