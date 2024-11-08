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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use function PHPSTORM_META\type;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Resolver\LoginMutation;
use App\State\UserProcessor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact', 'fname' => 'partial'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
        operations:[
            new Post(
                processor:UserProcessor::class
            ),
            new Get(security:"is_granted('ROLE_ADMIN')"),
            new GetCollection(security:"is_granted('ROLE_ADMIN')"),
            new Delete(),
            new Put(
                security:"is_granted('ROLE_ADMIN')",
                processor:"UserProcessor"
                ),
        ],
        graphQlOperations:[
            new Query(),
            new QueryCollection(paginationEnabled:\false),
            new Mutation(
                name:"create",
                processor:"UserProcessor"
                ),
            new Mutation(
                name:"update",
                processor: UserProcessor::class
                ),
            new Mutation(name:"delete"),
            new Mutation(name:"restore"),
            new QueryCollection(name:"collectionQuery",paginationEnabled:\false),
            new Mutation(
            name: 'login',
            resolver: LoginMutation::class,
            args: [
                'email' => ['type' => 'String!'],
                'password' => ['type' => 'String!'],
            ],
            description: 'Authenticate a user and return a JWT token'
        ),
        ],
        paginationEnabled:false,
// security: "is_granted('ROLE_ADMIN')",
// securityMessage: "Accès refusé",

)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180)]
    // #[Assert\NotBlank(message: "Email is required.")]
    // #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    // #[Assert\Unique(message:"cet email existe deja")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
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

    /**
     * @var Collection<int, Emprunts>
     */
    #[ORM\OneToMany(targetEntity: Emprunts::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $emprunts;

    /**
     * @var Collection<int, Abonnements>
     */
    #[ORM\OneToMany(targetEntity: Abonnements::class, mappedBy: 'abonne', orphanRemoval: true)]
    private Collection $abonnements;

     #[ORM\Column()]
     #[Assert\NotBlank]
     #[Assert\Length(
        min: 8,
        minMessage: "Password must be at least {{ limit }} characters long."
    )]
    private ?string $plainPassword = null;

    // private UserPasswordHasherInterface $passwordHasher;

    // public function __construct(UserPasswordHasherInterface $passwordHasher)
    // {
    //     $this->passwordHasher = $passwordHasher;
    //      $this->emprunts = new ArrayCollection();
    //     $this->abonnements = new ArrayCollection();
    // }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $emprunt->setUser($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunts $emprunt): static
    {
        if ($this->emprunts->removeElement($emprunt)) {
            // set the owning side to null (unless already changed)
            if ($emprunt->getUser() === $this) {
                $emprunt->setUser(null);
            }
        }

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
            $abonnement->setAbonne($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnements $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getAbonne() === $this) {
                $abonnement->setAbonne(null);
            }
        }

        return $this;
    }

    //  public function hashPassword(): void
    // {
    //     if ($this->plainPassword) {
    //         $this->password = $this->passwordHasher->hashPassword($this, $this->plainPassword);
    //         $this->plainPassword = null; // Clear plain password after hashing
    //     }
    // }
}
