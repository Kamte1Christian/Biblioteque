<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiFilter(SearchFilter::class, properties: ['categorie' => 'partial'])]
#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
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
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Unique()]
    private ?string $categorie = null;

    /**
     * @var Collection<int, Livres>
     */
    #[ORM\OneToMany(targetEntity: Livres::class, mappedBy: 'categorie')]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Livres>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livres $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setCategorie($this);
        }

        return $this;
    }

    public function removeLivre(Livres $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getCategorie() === $this) {
                $livre->setCategorie(null);
            }
        }

        return $this;
    }
}
