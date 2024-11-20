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
use App\State\SaveMediaObject;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File as File;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;

#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'auteur' => 'partial'])]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
   normalizationContext: ['groups' => ['media_object:read']],
    types: ['https://schema.org/MediaObject'],
    outputFormats: ['jsonld' => ['application/ld+json']],
    operations: [
               new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'String',
                                        'format' => 'binary'
                                    ],
                                    'title' => ['type' => 'String'],
                                    'Author' => ['type' => 'String'],
                                    'description' => ['type' => 'String'],
                                    'pages' => ['type' => 'Int'],
                                    'classe'=>['type'=>'String'],
                                    'categorie'=>['type'=>'String']
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Get(),
        new GetCollection( ),
        new Delete( ),
        new Put( ),
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
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $Author = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $pages = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isFree = null;

     #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_publication = null;

    #[ORM\Column(length: 300, nullable: true)]
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
    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['book:read'])]
    private ?string $contentUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[Groups(['book:write'])]
    public ?File $file = null;

    public function setFile(?File $file = null): static
    {
        $this->file = $file;

        if ($file) {
            // Automatically set the filePath using the original filename
            $this->filePath = uniqid() . '.' . $file->guessExtension(); // Ensures a unique filename

            // Automatically set the content URL based on the file path
            $this->contentUrl = '/images/covers' . $this->filePath; // Adjust the path as necessary
        }

        return $this;
    }

    // Other getters and setters...

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
        $this->date_publication = new DateTimeImmutable();

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

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(string $contentUrl): static
    {
        $this->contentUrl = $contentUrl;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;
        return $this;
    }
}
