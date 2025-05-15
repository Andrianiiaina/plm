<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $task = null;

    #[ORM\Column]
    private ?int $TenderId = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getTenderId(): ?int
    {
        return $this->TenderId;
    }

    public function setTenderId(int $TenderId): static
    {
        $this->TenderId = $TenderId;

        return $this;
    }
}
