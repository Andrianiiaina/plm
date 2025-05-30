<?php
    namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

    class HistoryEvent extends Event
    {
        public function __construct(
            private User $actor,
            private int $type,
            private int $type_id,
            private string $action,
        ) {}

        public function getActorId(): int
        {
            return $this->actor->getId();
        }
        public function getAction(): string
        {
            return $this->action;
        }
        public function getType(): string
        {
            return $this->type;
        }

        public function getTypeId(): string
        {
            return $this->type_id;
        }
    }

?>