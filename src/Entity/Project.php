<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $contract_number = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\Column]
    private ?float $min_budget = null;

    #[ORM\Column(nullable: true)]
    private ?float $max_budget = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private ?int $project_type = null;

    #[ORM\ManyToOne(inversedBy: 'url')]
    private ?User $responsable_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'project')]
    private Collection $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeImmutable $end_date): static
    {
        $this->end_date = $end_date;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProjectType(): ?int
    {
        return $this->project_type;
    }

    public function setProjectType(int $project_type): static
    {
        $this->project_type = $project_type;

        return $this;
    }

    public function getResponsableId(): ?User
    {
        return $this->responsable_id;
    }

    public function setResponsableId(?User $responsable_id): static
    {
        $this->responsable_id = $responsable_id;

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
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setProjectId($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getProject() === $this) {
                $file->setProjectId(null);
            }
        }

        return $this;
    }
}
