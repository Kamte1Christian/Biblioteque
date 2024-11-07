<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;

#[ORM\Entity(repositoryClass: ExemplairesRepository::class)]
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
            new QueryCollection(name:"collectionQuery",paginationEnabled:\false,resolver:"ExemplairesParLivreResolver")
        ],
        paginationEnabled:false,
security: "is_granted('ROLE_ADMIN')",
securityMessage: "Accès refusé",

)]
class Exemplaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    #[ORM\Column]
    private ?int $code_bar = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    private ?emprunts $emprunt = null;

    #[ORM\Column]
    private ?bool $state = false;

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

    public function getCodeBar(): ?int
    {
        return $this->code_bar;
    }

    public function setCodeBar(int $code_bar): static
    {
        $this->code_bar = $code_bar;

        return $this;
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

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }

}
