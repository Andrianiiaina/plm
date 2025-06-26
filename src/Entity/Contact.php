<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contact_perso = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contact_pro = null;

    #[ORM\Column(length: 100)]
    private ?string $organisation = null;

    #[ORM\Column(length: 100)]
    private ?string $function = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'contacts')]
    private ?self $parent = null;
    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $contacts;

    #[ORM\OneToOne(inversedBy: 'contact')]
    private ?User $user = null;

    /**
     * @var Collection<int, ContactGroup>
     */
    #[ORM\ManyToMany(targetEntity: ContactGroup::class, mappedBy: 'contacts')]
    private Collection $contactGroups;

    /**
     * @var Collection<int, Tender>
     */
    #[ORM\OneToMany(targetEntity: Tender::class, mappedBy: 'contact')]
    private Collection $tender;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->contactGroups = new ArrayCollection();
        $this->tender = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContactPerso(): ?string
    {
        return $this->contact_perso;
    }

    public function setContactPerso(?string $contact_perso): static
    {
        $this->contact_perso = $contact_perso;

        return $this;
    }

    public function getContactPro(): ?string
    {
        return $this->contact_pro;
    }

    public function setContactPro(?string $contact_pro): static
    {
        $this->contact_pro = $contact_pro;

        return $this;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(self $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setParent($this);
        }

        return $this;
    }

    public function removeContact(self $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            if ($contact->getParent() === $this) {
                $contact->setParent(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ContactGroup>
     */
    public function getContactGroups(): Collection
    {
        return $this->contactGroups;
    }

    public function addContactGroup(ContactGroup $contactGroup): static
    {
        if (!$this->contactGroups->contains($contactGroup)) {
            $this->contactGroups->add($contactGroup);
            $contactGroup->addContact($this);
        }

        return $this;
    }

    public function removeContactGroup(ContactGroup $contactGroup): static
    {
        if ($this->contactGroups->removeElement($contactGroup)) {
            $contactGroup->removeContact($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->email ?? 'N/A'; 
    }

    /**
     * @return Collection<int, Tender>
     */
    public function getTender(): Collection
    {
        return $this->tender;
    }

    public function addTender(Tender $tender): static
    {
        if (!$this->tender->contains($tender)) {
            $this->tender->add($tender);
            $tender->setContact($this);
        }

        return $this;
    }

    
    public function removeTender(Tender $tender): static
    {
        if ($this->tender->removeElement($tender)) {
            if ($tender->getContact() === $this) {
                $tender->setContact(null);
            }
        }

        return $this;
    }
}
