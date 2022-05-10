<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 150)]
    private $companyName;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $addressTwo;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $addressThree;

    #[ORM\ManyToOne(targetEntity: City::class)]
    private $city;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phoneCompany;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $emailCompany;

    #[ORM\Column(type: 'boolean')]
    private $sendCv;

    #[ORM\Column(type: 'boolean')]
    private $sendCoverletter;

    #[ORM\Column(type: 'text', nullable: true)]
    private $coverletterContent;

    #[ORM\Column(type: 'datetime')]
    private $createdDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false)]
    private $User;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Contact::class)]
    private $contacts;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: ApplicationNote::class)]
    private $applicationNotes;


    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->applicationNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddressTwo(): ?string
    {
        return $this->addressTwo;
    }

    public function setAddressTwo(?string $addressTwo): self
    {
        $this->addressTwo = $addressTwo;

        return $this;
    }

    public function getAddressThree(): ?string
    {
        return $this->addressThree;
    }

    public function setAddressThree(?string $addressThree): self
    {
        $this->addressThree = $addressThree;

        return $this;
    }

    public function getPhoneCompany(): ?string
    {
        return $this->phoneCompany;
    }

    public function setPhoneCompany(?string $phoneCompany): self
    {
        $this->phoneCompany = $phoneCompany;

        return $this;
    }

    public function getEmailCompany(): ?string
    {
        return $this->emailCompany;
    }

    public function setEmailCompany(?string $emailCompany): self
    {
        $this->emailCompany = $emailCompany;

        return $this;
    }

    public function isSendCv(): ?bool
    {
        return $this->sendCv;
    }

    public function setSendCv(bool $sendCv): self
    {
        $this->sendCv = $sendCv;

        return $this;
    }

    public function isSendCoverletter(): ?bool
    {
        return $this->sendCoverletter;
    }

    public function setSendCoverletter(bool $sendCoverletter): self
    {
        $this->sendCoverletter = $sendCoverletter;

        return $this;
    }

    public function getCoverletterContent(): ?string
    {
        return $this->coverletterContent;
    }

    public function setCoverletterContent(?string $coverletterContent): self
    {
        $this->coverletterContent = $coverletterContent;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCompany($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCompany() === $this) {
                $contact->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApplicationNote>
     */
    public function getApplicationNotes(): Collection
    {
        return $this->applicationNotes;
    }

    public function addApplicationNote(ApplicationNote $applicationNote): self
    {
        if (!$this->applicationNotes->contains($applicationNote)) {
            $this->applicationNotes[] = $applicationNote;
            $applicationNote->setCompany($this);
        }

        return $this;
    }

    public function removeApplicationNote(ApplicationNote $applicationNote): self
    {
        if ($this->applicationNotes->removeElement($applicationNote)) {
            // set the owning side to null (unless already changed)
            if ($applicationNote->getCompany() === $this) {
                $applicationNote->setCompany(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
