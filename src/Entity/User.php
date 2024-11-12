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
use App\Dto\LoginResponse;
use App\Resolver\LoginMutation;
use App\Resolver\LoginResolver;
use App\Resolver\TokenAuthentication;
use App\State\UserProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Post(processor: UserProcessor::class),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Delete(),
        new Put(security: "is_granted('ROLE_ADMIN')", processor: "UserProcessor"),
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
            name: 'login',
            resolver: LoginResolver::class,
            args: [
                'email' => ['type' => 'String!'],
                'password' => ['type' => 'String!'],
                'fname' => ['type' => 'String!']
            ],
            description: 'Log in a user and return a JWT token if successful.',
            output: LoginResponse::class
        ),
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

    #[ORM\OneToMany(targetEntity: Emprunts::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $emprunts;

    #[ORM\OneToMany(targetEntity: Abonnements::class, mappedBy: 'abonne', orphanRemoval: true)]
    private Collection $abonnements;

    public function __construct()
    {
        $this->emprunts = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
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

    public function getEmprunts(): Collection
    {
        return $this->emprunts;
    }

    public function addEmprunt(Emprunts $emprunt): static
    {
        if (!$this->emprunts->contains($emprunt)) {
            $this->emprunts->add($emprunt);
            $emprunt->setUser($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunts $emprunt): static
    {
        if ($this->emprunts->removeElement($emprunt)) {
            if ($emprunt->getUser() === $this) {
                $emprunt->setUser(null);
            }
        }

        return $this;
    }

    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnements $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setAbonne($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnements $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            if ($abonnement->getAbonne() === $this) {
                $abonnement->setAbonne(null);
            }
        }

        return $this;
    }
}
