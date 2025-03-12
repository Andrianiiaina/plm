<?php

namespace App\Entity;

use App\Repository\MilestoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: MilestoneRepository::class)]
class Milestone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $due_date = null;


    #[ORM\Column]
    private ?float $rate = 0;

    #[ORM\ManyToOne(inversedBy: 'milestones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $modifiedAt;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'milestone')]
    private Collection $tasks;

    #[ORM\ManyToOne(inversedBy: 'milestones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TaskStatus $status = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime(); 
        $this->modifiedAt = null;
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->due_date;
    }

    public function setDueDate(?\DateTimeInterface $due_date): static
    {
        $this->due_date = $due_date;

        return $this;
    }



    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

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

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setMilestone($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getMilestone() === $this) {
                $task->setMilestone(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function setStatus(?TaskStatus $status): static
    {
        $this->status = $status;

        return $this;
    }




    public function getMilestoneWeight(): float
    {
        
       return number_format($this->getRate() / $this->getProject()->getMilestonesWeight()*100);
    }

    public function totalTasksWeight(): float
    {
        $total_weight=0;
        foreach ($this->getTasks() as $milestone) {
            $total_weight+=$milestone->getRate();
        }
        return $total_weight;
    }

    public function getProgress(): float
    {
        $total_rate=0;
        $finished=0;
        foreach ($this->getTasks() as $task) {
           if($task->getStatus()->getCode()== "3")
            $finished += $task->getRate();
            $total_rate+=$task->getRate();
        }
       if($total_rate!=0){
             return  (number_format(($finished/$total_rate)*100));
        }else{
            return $this->getStatus()->getPercentage();
        }
    }

}
