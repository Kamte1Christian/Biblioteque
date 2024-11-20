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
use App\Controller\EmpruntController;
use App\Dto\CreateEmpruntInput;
use App\Resolver\EmpruntResolver;
use App\Repository\EmpruntRepository;
use App\Resolver\EmpruntEnCoursQuery;
use App\Resolver\ReturnExemplaireResolver;
use App\Resolver\UserEmpruntResolver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Type\Definition\Type;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/emprunter/create',
            controller: EmpruntController::class,
            input: CreateEmpruntInput::class,
            security: "is_granted('ROLE_ADMIN')",
            name: 'create_emprunt'
        ),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
    ],
    graphQlOperations: [
        new Query(),
        new QueryCollection(paginationEnabled: false),
        new Mutation(
            name: "emprunter",
            resolver: EmpruntResolver::class,
            args: [
                'Bookid' => [
                    'type' => '[Int]'
                ]
            ],

            description: "Permet à un utilisateur d'emprunter un exemplaire."
        ),
        new Mutation(
            name: "Backemprunt",
            resolver: ReturnExemplaireResolver::class,
            args: [
                'Userid' => [
                    'type' => 'Int'
                ],
                'codebars' => [
                    'type' => '[String]'
                ]
            ],
        ),
         new Mutation(
            name: "Useremprunter",
            resolver: UserEmpruntResolver::class,
            args: [
                'Userid' => [
                    'type' => 'Int'
                ],
                'Bookid' => [
                    'type' => '[Int]'
                ]
            ],

            description: "Permet au gestionnaire d'emprunter un exemplaire pour un utilisateur."
        ),
        new Mutation(name: "update"),
        new Mutation(name: "delete"),
        new Mutation(name: "restore"),
        new QueryCollection(name: "collectionQuery", paginationEnabled: false, resolver: EmpruntEnCoursQuery::class)
    ],
    paginationEnabled: false,
    security: "is_granted('ROLE_ADMIN')",
    securityMessage: "Accès refusé"
)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null; // Date de début de l'emprunt

    #[ORM\Column]
    private ?\DateTimeImmutable $normal_backAt = null; // Date normale de retour

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $effective_backAt = null; // Date effective de retour

    #[ORM\ManyToOne(inversedBy: 'Emprunt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null; // Utilisateur ayant fait l'emprunt

    #[ORM\Column]
    private ?bool $isBacked = null; // Statut : l'emprunt est-il retourné ?

    /**
     * @var Collection<int, Exemplaire> Liste des exemplaires empruntés
     */
    #[ORM\OneToMany(targetEntity: Exemplaire::class, mappedBy: 'emprunt',cascade:['PERSIST'],orphanRemoval:true)]
    private Collection $Exemplaire;

    public function __construct()
    {
        $this->Exemplaire = new ArrayCollection();
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
     * @return Collection<int, Exemplaire> Retourne les exemplaires empruntés
     */
    public function getExemplaire(): Collection
    {
        return $this->Exemplaire;
    }

    /**
     * Ajouter un exemplaire à l'emprunt
     */
    public function addExemplaire(Exemplaire $exemplaire): self
    {
        if ($this->Exemplaire->count() < 3 && !$this->Exemplaire->contains($exemplaire)) {
            $this->Exemplaire->add($exemplaire);
            $exemplaire->setEmprunt($this);
        }

        return $this;
    }

    /**
     * Retirer un exemplaire de l'emprunt
     */
    public function removeExemplaire(Exemplaire $exemplaire): static
    {
        if ($this->Exemplaire->removeElement($exemplaire)) {
            // Réinitialise la relation côté exemplaire
            if ($exemplaire->getEmprunt() === $this) {
                $exemplaire->setEmprunt(null);
            }
        }

        return $this;
    }
}
