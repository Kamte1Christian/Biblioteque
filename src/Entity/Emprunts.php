<?php

namespace App\Entity;

use App\Repository\EmpruntsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmpruntsRepository::class)]
class Emprunts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $normal_backAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $effective_backAt = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    #[ORM\Column]
    private ?bool $isBacked = null;

    /**
     * @var Collection<int, EmpruntExemplaire>
     */
    #[ORM\OneToMany(targetEntity: EmpruntExemplaire::class, mappedBy: 'emprunt', orphanRemoval: true)]
    private Collection $empruntExemplaires;

    public function __construct()
    {
        $this->empruntExemplaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getNormalBackAt(): ?\DateTimeImmutable
    {
        return $this->normal_backAt;
    }

    public function setNormalBackAt(\DateTimeImmutable $normal_backAt): static
    {
        $this->normal_backAt = $normal_backAt;

        return $this;
    }

    public function getEffectiveBackAt(): ?\DateTimeImmutable
    {
        return $this->effective_backAt;
    }

    public function setEffectiveBackAt(?\DateTimeImmutable $effective_backAt): static
    {
        $this->effective_backAt = $effective_backAt;

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


    public function isBacked(): ?bool
    {
        return $this->isBacked;
    }

    public function setBacked(bool $isBacked): static
    {
        $this->isBacked = $isBacked;

        return $this;
    }

    /**
     * @return Collection<int, EmpruntExemplaire>
     */
    public function getEmpruntExemplaires(): Collection
    {
        return $this->empruntExemplaires;
    }

    public function addEmpruntExemplaire(EmpruntExemplaire $empruntExemplaire): static
    {
        if (!$this->empruntExemplaires->contains($empruntExemplaire)) {
            $this->empruntExemplaires->add($empruntExemplaire);
            $empruntExemplaire->setEmprunt($this);
        }

        return $this;
    }

    public function removeEmpruntExemplaire(EmpruntExemplaire $empruntExemplaire): static
    {
        if ($this->empruntExemplaires->removeElement($empruntExemplaire)) {
            // set the owning side to null (unless already changed)
            if ($empruntExemplaire->getEmprunt() === $this) {
                $empruntExemplaire->setEmprunt(null);
            }
        }

        return $this;
    }
}
