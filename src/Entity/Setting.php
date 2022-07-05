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

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $label;

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
        return $this->cryptPassSym(false)->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;
        // Protection des valeurs dont la KeySetting contient "_pass"
        $this->cryptPassSym(true);
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Crypte et décrypte une chaîne de caractère
     * @param string $data Chaîne de caractère à traiter
     * @param bool $crypt True pour crypter / False pour décrypter
     * @return string Chaîne de caractère de résultat
     */
    private function cryptPassSym(bool $crypt): self
    {
        if (str_contains($this->keySetting, "_pass")) {
            // Data Crypt
            $cypher = "aes-256-cbc";
            $passphrase = "SettingPassword";
            $iv = "9988776655440099";
            if ($crypt) $this->value = openssl_encrypt($this->value, $cypher, $passphrase, 0, $iv);
            else {
                $userTemp = new Setting();
                $userTemp->setLabel($this->label);
                $userTemp->setValue($this->value);
                $userTemp->value = openssl_decrypt($this->value, $cypher, $passphrase, 0, $iv);
                return $userTemp;
            }
        }
        return $this;
    }
}
