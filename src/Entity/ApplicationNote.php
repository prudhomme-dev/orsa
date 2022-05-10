<?php

namespace App\Entity;

use App\Repository\ApplicationNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationNoteRepository::class)]
class ApplicationNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\Column(type: 'text', nullable: true)]
    private $messageNote;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'applicationNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $Status;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'applicationNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMessageNote(): ?string
    {
        return $this->messageNote;
    }

    public function setMessageNote(?string $messageNote): self
    {
        $this->messageNote = $messageNote;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->Status;
    }

    public function setStatus(?Status $Status): self
    {
        $this->Status = $Status;

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
