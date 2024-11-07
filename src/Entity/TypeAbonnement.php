<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use App\Repository\TypeAbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeAbonnementRepository::class)]
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
            new QueryCollection(paginationEnabled:\false),
            new Mutation(name:"create"),
            new Mutation(name:"update"),
            new Mutation(name:"delete"),
            new Mutation(name:"restore"),
            new QueryCollection(name:"collectionQuery",paginationEnabled:\false)
        ],
        paginationEnabled:false,
        security: "is_granted('ROLE_ADMIN')",
        securityMessage: "Accès refusé",
    )]
class TypeAbonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
     #[Assert\Unique()]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $duree_jours = null;

    /**
     * @var Collection<int, Abonnements>
     */
    #[ORM\OneToMany(targetEntity: Abonnements::class, mappedBy: 'type')]
    private Collection $abonnements;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDureeJours(): ?int
    {
        return $this->duree_jours;
    }

    public function setDureeJours(int $duree_jours): static
    {
        $this->duree_jours = $duree_jours;

        return $this;
    }

    /**
     * @return Collection<int, Abonnements>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnements $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setType($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnements $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getType() === $this) {
                $abonnement->setType(null);
            }
        }

        return $this;
    }
}
