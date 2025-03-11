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

    #[ORM\Column]
    private ?float $budget = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?User $responsable = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deadline = null;

    /**
     * @var Collection<int, Milestone>
     */
    #[ORM\OneToMany(targetEntity: Milestone::class, mappedBy: 'project')]
    private Collection $milestones;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $modifiedAt;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProjectStatus $status = null;

    #[ORM\Column(length: 50)]
    private ?string $devise = 'EUR';

    public function __construct()
    {
        $this->milestones = new ArrayCollection();
        $this->createdAt = new \DateTime(); // Mettre la date actuelle par défaut
        $this->modifiedAt = null;
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

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @return Collection<int, Milestone>
     */
    public function getMilestones(): Collection
    {
        return $this->milestones;
    }

    public function addMilestone(Milestone $milestone): static
    {
        if (!$this->milestones->contains($milestone)) {
            $this->milestones->add($milestone);
            $milestone->setProject($this);
        }

        return $this;
    }

    public function removeMilestone(Milestone $milestone): static
    {
        if ($this->milestones->removeElement($milestone)) {
            // set the owning side to null (unless already changed)
            if ($milestone->getProject() === $this) {
                $milestone->setProject(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTime
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTime $modified_at): static
    {
        $this->modifiedAt = $modified_at;

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProgress(): float
    {
        $total_rate=0;
        $milestone_points=0;
        foreach ($this->getMilestones() as $milestone) {
           $total_rate+=($milestone->getProgress()*$milestone->getRate());

           $milestone_points+=$milestone->getRate();
        }

        if($total_rate!=0){
             return (number_format($total_rate/$milestone_points));
        }else{
             return $this->getStatus()->getPercentage();
        }
    }
    
    public function getMilestonesWeight(): float
    {
        $total_weight=0;
        foreach ($this->getMilestones() as $milestone) {
            $total_weight+=$milestone->getRate();
        }
        return $total_weight;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): static
    {
        $this->devise = $devise;

        return $this;
    }
   
}
