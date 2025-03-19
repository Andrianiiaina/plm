<?php

namespace App\Entity;

use App\Repository\AllotissementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllotissementRepository::class)]
class Allotissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = "";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = "";

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = "";

    #[ORM\Column(nullable: true)]
    private ?int $minBudget = 0;

    #[ORM\Column(nullable: true)]
    private ?int $maxBudget = 0;

    #[ORM\ManyToOne]
    private ?Tender $tender = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMinBudget(): ?int
    {
        return $this->minBudget;
    }

    public function setMinBudget(?int $minBudget): static
    {
        $this->minBudget = $minBudget;

        return $this;
    }

    public function getMaxBudget(): ?int
    {
        return $this->maxBudget;
    }

    public function setMaxBudget(?int $maxBudget): static
    {
        $this->maxBudget = $maxBudget;

        return $this;
    }

    public function getTender(): ?Tender
    {
        return $this->tender;
    }

    public function setTender(?Tender $tender): static
    {
        $this->tender = $tender;

        return $this;
    }
}
