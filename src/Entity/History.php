<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $actor_id = null;

    #[ORM\Column(length: 255)]
    private ?string $actions = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $date;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private ?int $type_id = null;

    public function __construct() {
        $this->date=new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActorId(): ?int
    {
        return $this->actor_id;
    }

    public function setActorId(?int $actor_id): static
    {
        $this->actor_id = $actor_id;

        return $this;
    }

    public function getActions(): ?string
    {
        return $this->actions;
    }

    public function setActions(string $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): static
    {
        $this->type_id = $type_id;

        return $this;
    }
}
