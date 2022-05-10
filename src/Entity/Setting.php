<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 70)]
    private $keySetting;

    #[ORM\Column(type: 'text', nullable: true)]
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeySetting(): ?string
    {
        return $this->keySetting;
    }

    public function setKeySetting(string $keySetting): self
    {
        $this->keySetting = $keySetting;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
