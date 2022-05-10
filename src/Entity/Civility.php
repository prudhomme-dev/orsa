<?php

namespace App\Entity;

use App\Repository\CivilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CivilityRepository::class)]
class Civility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $nameCivility;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCivility(): ?string
    {
        return $this->nameCivility;
    }

    public function setNameCivility(string $nameCivility): self
    {
        $this->nameCivility = $nameCivility;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nameCivility;
    }
}
