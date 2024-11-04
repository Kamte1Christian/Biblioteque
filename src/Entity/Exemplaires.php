<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplairesRepository::class)]
class Exemplaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    /**
     * @var Collection<int, Emprunts>
     */
    #[ORM\OneToMany(targetEntity: Emprunts::class, mappedBy: 'exemplaire')]
    private Collection $emprunts;

    #[ORM\Column]
    private ?int $code_bar = null;

    /**
     * @var Collection<int, EmpruntExemplaire>
     */
    #[ORM\OneToMany(targetEntity: EmpruntExemplaire::class, mappedBy: 'exemplaire')]
    private Collection $empruntExemplaires;

    public function __construct()
    {
        $this->emprunts = new ArrayCollection();
        $this->empruntExemplaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;

        return $this;
    }

    /**
     * @return Collection<int, Emprunts>
     */
    public function getEmprunts(): Collection
    {
        return $this->emprunts;
    }

    public function addEmprunt(Emprunts $emprunt): static
    {
        if (!$this->emprunts->contains($emprunt)) {
            $this->emprunts->add($emprunt);
            $emprunt->setExemplaire($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunts $emprunt): static
    {
        if ($this->emprunts->removeElement($emprunt)) {
            // set the owning side to null (unless already changed)
            if ($emprunt->getExemplaire() === $this) {
                $emprunt->setExemplaire(null);
            }
        }

        return $this;
    }

    public function getCodeBar(): ?int
    {
        return $this->code_bar;
    }

    public function setCodeBar(int $code_bar): static
    {
        $this->code_bar = $code_bar;

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
            $empruntExemplaire->setExemplaire($this);
        }

        return $this;
    }

    public function removeEmpruntExemplaire(EmpruntExemplaire $empruntExemplaire): static
    {
        if ($this->empruntExemplaires->removeElement($empruntExemplaire)) {
            // set the owning side to null (unless already changed)
            if ($empruntExemplaire->getExemplaire() === $this) {
                $empruntExemplaire->setExemplaire(null);
            }
        }

        return $this;
    }
}
