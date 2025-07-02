<?php

namespace App\Entity;

use App\Repository\ReminderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReminderRepository::class)]
class Reminder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $date_type = null;

    #[ORM\Column]
    private ?int $day_before = null;

    #[ORM\ManyToOne(inversedBy: 'reminder')]
    private ?TenderDate $tenderDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $reminder_date = null;

    const DATE_TYPE_SUBMISSION = 0;
    const DATE_TYPE_NEGOCIATION = 1;
    const DATE_TYPE_RESPONSE = 2;
    const DATE_TYPE_ATTRIBUTION=3;
    const DATE_TYPE_START = 4;
    const DATE_TYPE_END = 5;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDateType(): ?int
    {
        return $this->date_type;
    }

    public function setDateType(int $date_type): static
    {
        $this->date_type = $date_type;

        return $this;
    }

    public function getDayBefore(): ?int
    {
        return $this->day_before;
    }

    public function setDayBefore(int $day_before): static
    {
        $this->day_before = $day_before;

        return $this;
    }

    public function getTenderDate(): ?TenderDate
    {
        return $this->tenderDate;
    }

    public function setTenderDate(?TenderDate $tenderDate): static
    {
        $this->tenderDate = $tenderDate;

        return $this;
    }

    public function getReminderDate(): ?\DateTimeInterface
    {
        return $this->reminder_date;
    }

    public function setReminderDate(?\DateTimeInterface $reminder_date): static
    {
        $this->reminder_date = $reminder_date;

        return $this;
    }

}
