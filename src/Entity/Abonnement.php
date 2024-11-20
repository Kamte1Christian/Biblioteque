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
use App\Controller\CreateAbonnementController;
use App\Dto\CreateAbonnementInput;
use App\Repository\AbonnementRepository;
use App\Resolver\CreateAbonnementResolver;
use DateTimeImmutable;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Builder\Method;
use PhpParser\Node\Arg;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ApiResource(
    operations:[
         new Post(
            uriTemplate: '/abonnements/create',
            denormalizationContext: ['groups' => ['abonnement:create']],
            controller: CreateAbonnementController::class,
            input: CreateAbonnementInput::class,
            security: "is_granted('ROLE_ADMIN')",
            name: 'create_abonnement',
            output:false,

        ),
        new Get(security:"is_granted('ROLE_ADMIN')"),
        new GetCollection(security:"is_granted('ROLE_ADMIN')"),
        new Delete(security:"is_granted('ROLE_ADMIN')"),
        new Put(security:"is_granted('ROLE_ADMIN')"),
    ],
    graphQlOperations:[
    new Query(),
    new QueryCollection(),
    new Mutation(name:"update"),
    new Mutation(name:"delete"),
    new Mutation(name:"restore"),
    new Mutation(
            name: 'createAbonnement',
            resolver: CreateAbonnementResolver::class,
            args:[
                'type'=>['type'=>'Int']
            ]
        ),
],
paginationEnabled:false,
security: "is_granted('ROLE_ADMIN')",
securityMessage: "Accès refusé",

)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Abonnement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $abonne = null;

    #[ORM\ManyToOne(inversedBy: 'Abonnement')]
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

    public function getAbonne(): ?User
    {
        return $this->abonne;
    }

    public function setAbonne(?User $abonne): static
    {
        $this->abonne = $abonne;

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
