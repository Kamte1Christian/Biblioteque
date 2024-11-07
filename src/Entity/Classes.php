<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use App\Repository\ClassesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use Symfony\Component\Validator\Constraints as Assert;
use function PHPSTORM_META\type;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiFilter(SearchFilter::class, properties: ['classe' => 'partial'])]
#[ORM\Entity(repositoryClass: ClassesRepository::class)]
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
class Classes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Unique()]
    private ?string $classe = null;

    /**
     * @var Collection<int, Livres>
     */
    #[ORM\OneToMany(targetEntity: Livres::class, mappedBy: 'classe')]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClasse(): ?string
    {
        return $this->classe;
    }

    public function setClasse(string $classe): static
    {
        $this->classe = $classe;

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
            $livre->setClasse($this);
        }

        return $this;
    }

    public function removeLivre(Livres $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getClasse() === $this) {
                $livre->setClasse(null);
            }
        }

        return $this;
    }
}
