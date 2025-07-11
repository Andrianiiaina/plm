<?php

namespace App\Entity;

use App\Repository\TenderDateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TenderDateRepository::class)]
class TenderDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $submissionDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $responseDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $attributionDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $negociationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\OneToOne(inversedBy: 'tenderDate',cascade: ['persist', 'remove'])]
    private ?Tender $tender = null;


    #[ORM\Column]
    private ?float $duration = 0;

    /**
     * @var Collection<int, Reminder>
     */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'tenderDate', cascade: ['persist','remove'])]
    private Collection $reminder;

    public function __construct()
    {
        $this->reminder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubmissionDate(): ?\DateTimeInterface
    {
        return $this->submissionDate;
    }

    public function setSubmissionDate(?\DateTimeInterface $submissionDate): static
    {
        $this->submissionDate = $submissionDate;

        return $this;
    }

    public function getResponseDate(): ?\DateTimeInterface
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeInterface $responseDate): static
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function getAttributionDate(): ?\DateTimeInterface
    {
        return $this->attributionDate;
    }

    public function setAttributionDate(?\DateTimeInterface $attributionDate): static
    {
        $this->attributionDate = $attributionDate;

        return $this;
    }

    public function getNegociationDate(): ?\DateTimeInterface
    {
        return $this->negociationDate;
    }

    public function setNegociationDate(?\DateTimeInterface $negociationDate): static
    {
        $this->negociationDate = $negociationDate;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

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


    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
    public function __toString(): string
    {
        return $this->id; 
    }

    /**
     * @return Collection<int, Reminder>
     */
    public function getReminder(): Collection
    {
        return $this->reminder;
    }

    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminder->contains($reminder)) {
            $this->reminder->add($reminder);
            $reminder->setTenderDate($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminder->removeElement($reminder)) {
            if ($reminder->getTenderDate() === $this) {
                $reminder->setTenderDate(null);
            }
        }

        return $this;
    }
}
