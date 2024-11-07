<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\EmpruntsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmpruntsRepository::class)]
#[ApiResource(
    operations:[
        new Post(security:"is_granted('ROLE_ADMIN')"),
        new Get(security:"is_granted('ROLE_ADMIN')"),
        new GetCollection(security:"is_granted('ROLE_ADMIN')"),
        new Delete(security:"is_granted('ROLE_ADMIN')"),
        new Put(security:"is_granted('ROLE_ADMIN')"),
    ],
graphQlOperations:[
    new Query(),
    new QueryCollection(paginationEnabled:false),
    new Mutation(name:"create"),
    new Mutation(name:"update"),
    new Mutation(name:"delete"),
    new Mutation(name:"restore"),
    new QueryCollection(name:"collectionQuery",paginationEnabled:false, resolver:"EmpruntsEnCoursQuery")
],
paginationEnabled:false,
security: "is_granted('ROLE_ADMIN')",
securityMessage: "Accès refusé",


)]
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
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'emprunt')]
    private Collection $exemplaires;

    public function __construct()
    {
        $this->exemplaires = new ArrayCollection();
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
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaire(Exemplaires $exemplaire): self
    {
        if ($this->exemplaires->count() < 3) {
            $this->exemplaires->add($exemplaire);
            $exemplaire->setEmprunt($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->exemplaires->removeElement($exemplaire)) {
            // set the owning side to null (unless already changed)
            if ($exemplaire->getEmprunt() === $this) {
                $exemplaire->setEmprunt(null);
            }
        }

        return $this;
    }

}
