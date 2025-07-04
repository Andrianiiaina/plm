<?php

namespace App\Entity;

use App\Repository\TenderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TenderRepository::class)]
#[UniqueEntity(fields: ['contract_number'], message: 'Ce référence est déja utilisé.')]
class Tender
{
    const TO_RESUMED = 0;
    const TO_SUBMITED=1;
    const SUBMITED=2;
    const LOST=3;
    const WON=4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = "";

    #[ORM\Column(length: 255, unique:true)]
    private ?string $contract_number = "";

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = "";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = "";


    #[ORM\Column]
    private ?float $min_budget = 0;

    #[ORM\Column(nullable: true)]
    private ?float $max_budget = 0;

    #[ORM\Column]
    private ?string $status = "0";

    #[ORM\Column]
    private ?string $tender_type = "0";

    #[ORM\ManyToOne(inversedBy: 'tenders')]
    private ?User $responsable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = "";

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;


    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'tender', cascade:['persist','remove'])]
    private Collection $documents;


    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'tender', cascade:['persist','remove'])]
    private Collection $files;



    /**
     * @var Collection<int, Calendar>
     */
    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'tender', cascade:['persist','remove'])]
    private Collection $calendars;


   /**
     * @var Collection<int, Allotissement>
     */
    #[ORM\OneToMany(targetEntity: Allotissement::class, mappedBy: 'tender', cascade:['persist','remove'])]
    private Collection $allotissements;


    #[ORM\Column]
    private ?bool $isArchived = false;

    #[ORM\OneToOne(mappedBy: 'tender', cascade: ['persist', 'remove'])]
    private ?TenderDate $tenderDate = null;

    #[ORM\ManyToOne(inversedBy: 'tender')]
    private ?Contact $contact = null;
    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->createdAt = new \DateTime(); // Mettre la date actuelle par défaut
    }
    public function __toString(): string
    {
        return $this->contract_number ?? 'N/A'; 
    }


    #[Assert\Callback]
    public function validateBudget(ExecutionContextInterface $context): void
    {
        if ($this->min_budget && $this->max_budget && $this->min_budget >= $this->max_budget) {
            $context->buildViolation('La budget min doit être inferieur au budget max.')
                ->atPath('max_budget')
                ->addViolation();
        }
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContractNumber(): ?string
    {
        return $this->contract_number;
    }

    public function setContractNumber(string $contract_number): static
    {
        $this->contract_number = $contract_number;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getMinBudget(): ?float
    {
        return $this->min_budget;
    }

    public function setMinBudget(float $min_budget): static
    {
        $this->min_budget = $min_budget;

        return $this;
    }

    public function getMaxBudget(): ?float
    {
        return $this->max_budget;
    }

    public function setMaxBudget(?float $max_budget): static
    {
        $this->max_budget = $max_budget;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTenderType(): ?string
    {
        return $this->tender_type;
    }

    public function setTenderType(string $tender_type): static
    {
        $this->tender_type = $tender_type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?\DateTime $date): static
    {
        $this->createdAt = $date;

        return $this;
    }
    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
    

     /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

   

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getTenderDate(): ?TenderDate
    {
        return $this->tenderDate;
    }

    public function setTenderDate(?TenderDate $tenderDate): static
    {
        if ($tenderDate === null && $this->tenderDate !== null) {
            $this->tenderDate->setTender(null);
        }

        if ($tenderDate !== null && $tenderDate->getTender() !== $this) {
            $tenderDate->setTender($this);
        }

        $this->tenderDate = $tenderDate;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

}
