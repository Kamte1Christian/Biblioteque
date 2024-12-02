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
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Patch;
use App\Dto\LoginResponse;
use App\Resolver\CreateAbonnementResolver;
use App\Resolver\LoginMutation;
use App\Resolver\LoginResolver;
use App\Resolver\TokenAuthentication;
use App\State\UserProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact', 'fname' => 'partial'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Post(processor: UserProcessor::class),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Delete(),
        new Put(
            uriTemplate: '/users/{id}',
            processor: UserProcessor::class
        ),
        new Patch(
            processor: UserProcessor::class
            )
    ],
    graphQlOperations: [
        new Query(),
        new QueryCollection(paginationEnabled: false),
        new Mutation(
            name: "create",
            processor: UserProcessor::class
        ),
        new Mutation(
            name: "update",
            processor: UserProcessor::class
        ),
        new Mutation(name: "delete"),
        new Mutation(name: "restore"),
        new QueryCollection(name: "collectionQuery", paginationEnabled: false),
        new Mutation(
            name: 'connectWithToken',
            resolver: TokenAuthentication::class,
            args: [
                'token' => ['type' => 'String!'],
            ],
            description: 'Authenticate a user using a JWT token'
        ),
    ],
    paginationEnabled: false
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    #[Assert\Length(
        min: 8,
        minMessage: "Password must be at least {{ limit }} characters long."
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "First name cannot exceed {{ limit }} characters."
    )]
    private ?string $fname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lname = null;

    #[ORM\OneToMany(targetEntity: Emprunt::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $Emprunt;

    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'abonne', orphanRemoval: true)]
    private Collection $Abonnement;

    /**
     * @var Collection<int, Notation>
     */
    #[ORM\OneToMany(targetEntity: Notation::class, mappedBy: 'User')]
    private Collection $notations;

    public function __construct()
    {
        $this->Emprunt = new ArrayCollection();
        $this->Abonnement = new ArrayCollection();
        $this->notations = new ArrayCollection();
    }

    // Password handling (hashing logic)
    public function hashPassword(UserPasswordHasherInterface $passwordHasher): void
    {
        if ($this->password) {
            $this->password = $passwordHasher->hashPassword($this, $this->password);
            $this->password = null; // Clear the plain password after hashing
        }
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function getFname(): ?string
    {
        return $this->fname;
    }

    public function setFname(string $fname): static
    {
        $this->fname = $fname;

        return $this;
    }

    public function getLname(): ?string
    {
        return $this->lname;
    }

    public function setLname(?string $lname): static
    {
        $this->lname = $lname;

        return $this;
    }

    public function getEmprunt(): Collection
    {
        return $this->Emprunt;
    }

    public function addEmprunt(Emprunt $emprunt): static
    {
        if (!$this->Emprunt->contains($emprunt)) {
            $this->Emprunt->add($emprunt);
            $emprunt->setUser($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunt $emprunt): static
    {
        if ($this->Emprunt->removeElement($emprunt)) {
            if ($emprunt->getUser() === $this) {
                $emprunt->setUser(null);
            }
        }

        return $this;
    }

    public function getAbonnement(): Collection
    {
        return $this->Abonnement;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->Abonnement->contains($abonnement)) {
            $this->Abonnement->add($abonnement);
            $abonnement->setAbonne($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->Abonnement->removeElement($abonnement)) {
            if ($abonnement->getAbonne() === $this) {
                $abonnement->setAbonne(null);
            }
        }

        return $this;
    }

     public function hasActiveAbonnement(): bool
    {
        $now = new \DateTimeImmutable();

        foreach ($this->Abonnement as $abonnement) {
            if ($abonnement->getDateDebut() <= $now && $abonnement->getDateFin() >= $now) {
                return true; // Abonnement actif trouvé
            }
        }

        return false; // Aucun abonnement actif
    }

     /**
      * @return Collection<int, Notation>
      */
     public function getNotations(): Collection
     {
         return $this->notations;
     }

     public function addNotation(Notation $notation): static
     {
         if (!$this->notations->contains($notation)) {
             $this->notations->add($notation);
             $notation->setUser($this);
         }

         return $this;
     }

     public function removeNotation(Notation $notation): static
     {
         if ($this->notations->removeElement($notation)) {
             // set the owning side to null (unless already changed)
             if ($notation->getUser() === $this) {
                 $notation->setUser(null);
             }
         }

         return $this;
     }
}
