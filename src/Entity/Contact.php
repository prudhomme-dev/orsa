<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 70, nullable: true)]
    private $contactFirstname;

    #[ORM\Column(type: 'string', length: 70)]
    private $contactLastname;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $contactEmail;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $contactPhone;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $contactMobilePhone;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $contactFunction;

    #[ORM\ManyToOne(targetEntity: Civility::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $Civility;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactFirstname(): ?string
    {
        return $this->contactFirstname;
    }

    public function setContactFirstname(?string $contactFirstname): self
    {
        $this->contactFirstname = $contactFirstname;

        return $this;
    }

    public function getContactLastname(): ?string
    {
        return $this->contactLastname;
    }

    public function setContactLastname(string $contactLastname): self
    {
        $this->contactLastname = $contactLastname;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactMobilePhone(): ?string
    {
        return $this->contactMobilePhone;
    }

    public function setContactMobilePhone(?string $contactMobilePhone): self
    {
        $this->contactMobilePhone = $contactMobilePhone;

        return $this;
    }

    public function getContactFunction(): ?string
    {
        return $this->contactFunction;
    }

    public function setContactFunction(?string $contactFunction): self
    {
        $this->contactFunction = $contactFunction;

        return $this;
    }

    public function getCivility(): ?Civility
    {
        return $this->Civility;
    }

    public function setCivility(?Civility $Civility): self
    {
        $this->Civility = $Civility;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
