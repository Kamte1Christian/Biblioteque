<?php

namespace App\Entity;

use App\Repository\EmpruntExemplaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntExemplaireRepository::class)]
class EmpruntExemplaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'empruntExemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?emprunts $emprunt = null;

    #[ORM\ManyToOne(inversedBy: 'empruntExemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?exemplaires $exemplaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $normal_back_At = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $effctive_back_At = null;

    #[ORM\Column]
    private ?bool $isBacked = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmprunt(): ?emprunts
    {
        return $this->emprunt;
    }

    public function setEmprunt(?emprunts $emprunt): static
    {
        $this->emprunt = $emprunt;

        return $this;
    }

    public function getExemplaire(): ?exemplaires
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?exemplaires $exemplaire): static
    {
        $this->exemplaire = $exemplaire;

        return $this;
    }

    public function getNormalBackAt(): ?\DateTimeImmutable
    {
        return $this->normal_back_At;
    }

    public function setNormalBackAt(\DateTimeImmutable $normal_back_At): static
    {
        $this->normal_back_At = $normal_back_At;

        return $this;
    }

    public function getEffctiveBackAt(): ?\DateTimeImmutable
    {
        return $this->effctive_back_At;
    }

    public function setEffctiveBackAt(?\DateTimeImmutable $effctive_back_At): static
    {
        $this->effctive_back_At = $effctive_back_At;

        return $this;
    }

    public function isBacked(): ?bool
    {
        return $this->isBacked;
    }

    public function setBacked(bool $isBacked): static
    {
        $this->isBacked = $isBacked;

        return $this;
    }
}
