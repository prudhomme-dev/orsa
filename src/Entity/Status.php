<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 70)]
    private $statusName;

    #[ORM\OneToMany(mappedBy: 'Status', targetEntity: ApplicationNote::class)]
    private $applicationNotes;

    public function __construct()
    {
        $this->applicationNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusName(): ?string
    {
        return $this->statusName;
    }

    public function setStatusName(string $statusName): self
    {
        $this->statusName = $statusName;

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
            $applicationNote->setStatus($this);
        }

        return $this;
    }

    public function removeApplicationNote(ApplicationNote $applicationNote): self
    {
        if ($this->applicationNotes->removeElement($applicationNote)) {
            // set the owning side to null (unless already changed)
            if ($applicationNote->getStatus() === $this) {
                $applicationNote->setStatus(null);
            }
        }

        return $this;
    }
}
