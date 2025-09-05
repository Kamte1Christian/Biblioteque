<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\OpenApi\Model;
use App\Resolver\AverageScoreResolver;
use App\State\SaveMediaObject;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File as File;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;

#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'auteur' => 'partial'])]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext:['groups'=>['book:read']],
    outputFormats: ['jsonld' => ['application/ld+json']],
    operations: [
        // Upload Front Cover
        new Post(
             uriTemplate: '/books/{id}/front-cover-update',
             name: 'cover_upload',
            denormalizationContext: ['groups' => ['book:update:front']],
            inputFormats: ['multipart' => ['multipart/form-data']],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'coverImageFile' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Post(
             uriTemplate: '/books/{id}/back-cover-update',
             name: 'content_upload',
            denormalizationContext: ['groups' => ['book:update:back']],
            inputFormats: ['multipart' => ['multipart/form-data']],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'bookFileField' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        // Autres opérations
        new Post(
            denormalizationContext:['groups' => ['book:create']],
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
        ),
        new Get(),
        new GetCollection(),
        new Delete(),
        new Put(),
    ],
    graphQlOperations:[
        new Query(),
        new QueryCollection(paginationEnabled:false),
        new Mutation(
            name:"create",
            denormalizationContext:['groups' => ['book:create']]
            ),
            new Mutation(
            normalizationContext:['groups'=>['book:read']],
            name: "getAverageScore",
            resolver: AverageScoreResolver::class,
            args: [
                'Bookid' => [
                    'type' => 'Int!',
                    'description' => 'The id of the book',
                ],
            ],
        ),
        new Mutation(name:"update"),
        new Mutation(name:"delete"),
        new Mutation(name:"restore"),
        new QueryCollection(name:"collectionQuery",paginationEnabled:false)
    ],
    paginationEnabled:false,
    // security: "is_granted('ROLE_ADMIN')",
    // securityMessage: "Accès refusé",
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book:create'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book:create'])]
    private ?string $Author = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['book:create'])]
    private ?int $pages = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(['book:create'])]
    private ?bool $isFree = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_publication = null;

    #[ORM\Column(length: 300, nullable: true)]
    #[Groups(['book:create'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Exemplaire>
     */
    #[ORM\OneToMany(targetEntity: Exemplaire::class, mappedBy: 'Book', orphanRemoval: true)]
    private Collection $Exemplaire;

    #[ORM\ManyToOne(inversedBy: 'Book')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'Book')]
    private ?Classe $classe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[Vich\UploadableField(mapping: "front_cover_upload", fileNameProperty: "coverImage")]
    #[Groups(['book:update:front'])]
    private ?File $coverImageFile = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bookFile = null;

    #[Vich\UploadableField(mapping: "back_cover_upload", fileNameProperty: "bookFile")]
    #[Groups(['book:update:back'])]
    #[Assert\File(
        maxSize: "10M",
    )]
    private ?File $bookFileField = null;

    /**
     * @var Collection<int, Notation>
     */
    #[ORM\OneToMany(targetEntity: Notation::class, mappedBy: 'Book')]
    private Collection $notations;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read'])]
    private ?int $Averagescore = null;


    public function __construct()
    {
        $this->Exemplaire = new ArrayCollection();
        $this->date_publication = new DateTimeImmutable();
        $this->notations = new ArrayCollection();
    }

    // Autres getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->Author;
    }

    public function setAuthor(string $Author): static
    {
        $this->Author = $Author;
        return $this;
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(?int $pages): static
    {
        $this->pages = $pages;
        return $this;
    }

    public function isFree(): ?bool
    {
        return $this->isFree;
    }

    public function setFree(bool $isFree): static
    {
        $this->isFree = $isFree;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeImmutable $date_publication): static
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Exemplaire>
     */
    public function getExemplaire(): Collection
    {
        return $this->Exemplaire;
    }

    public function addExemplaire(Exemplaire $exemplaire): static
    {
        if (!$this->Exemplaire->contains($exemplaire)) {
            $this->Exemplaire->add($exemplaire);
            $exemplaire->setBook($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaire $exemplaire): static
    {
        if ($this->Exemplaire->removeElement($exemplaire)) {
            // set the owning side to null (unless already changed)
            if ($exemplaire->getBook() === $this) {
                $exemplaire->setBook(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;
        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getCoverImageFile(): ?File
    {
        return $this->coverImageFile;
    }

    public function setCoverImageFile(?File $coverImageFile): static
    {
        $this->coverImageFile = $coverImageFile;

        if ($coverImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
            $this->coverImage = uniqid() . '.' . $coverImageFile->guessExtension();
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

      public function getBookFile(): ?string
    {
        return $this->bookFile;
    }

    public function setBookFile(?string $bookFile): static
    {
        $this->bookFile = $bookFile;

        return $this;
    }

    public function getBookFileField(): ?File
    {
        return $this->bookFileField;
    }

    public function setBookFileField(?File $bookFileField): static
    {
        $this->bookFileField = $bookFileField;

        if ($bookFileField) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
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
            $notation->setBook($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): static
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getBook() === $this) {
                $notation->setBook(null);
            }
        }

        return $this;
    }

    public function getAveragescore(): ?int
    {
        return $this->Averagescore;
    }

    public function setAveragescore(?int $Averagescore): static
    {
        $this->Averagescore = $Averagescore;

        return $this;
    }
}
