<?php
    namespace App\Event;

    use App\Entity\User;
    use Symfony\Contracts\EventDispatcher\Event;

    class UserAssignedToEntityEvent extends Event
    {
        public function __construct(
            private User $user,
            private string $type,
            private int $id_type
        ) {}

        public function getUser(): User
        {
            return $this->user;
        }
        public function getType(): string
        {
            return $this->type;
        }
        public function getIdType(): int
        {
            return $this->id_type;
        }

       
    }

?>