<?php

namespace App\Entity;

use App\Repository\AbonnementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementsRepository::class)]
class Abonnements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $abonné = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeAbonnement $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_debut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_fin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonné(): ?User
    {
        return $this->abonné;
    }

    public function setAbonné(?User $abonné): static
    {
        $this->abonné = $abonné;

        return $this;
    }

    public function getType(): ?TypeAbonnement
    {
        return $this->type;
    }

    public function setType(?TypeAbonnement $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeImmutable $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeImmutable $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }
}
