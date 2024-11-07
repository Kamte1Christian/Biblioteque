<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use App\Repository\LivresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\OpenApi\Model;
use App\State\SaveMediaObject;

#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact', 'fname' => 'partial'])]
#[ORM\Entity(repositoryClass: LivresRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations:[
        new Post(security:"is_granted('ROLE_ADMIN')"),
         new Post(
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
                                    ],
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                    'auteur' => [
                                        'type' => 'string'
                                    ],
                                    'description' => [
                                        'type' => 'string'
                                    ],
                                    'date_publication' => [
                                        'type' => 'DateTimeImmutable'
                                    ],
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
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
class Livres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $auteur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_publication = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'livre', orphanRemoval: true)]
    private Collection $exemplaires;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    private ?categories $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    private ?classes $classe = null;

     #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $coverUrl = null;

     #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $editionKey = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $coverImageName = null;

    #[Vich\UploadableField(mapping: 'livre_cover_image', fileNameProperty: 'coverImageName')]
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Please upload a valid image (JPEG or PNG)."
    )]
    private ?File $coverImageFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function setCoverImageFile(?File $coverImageFile = null): void
    {
        $this->coverImageFile = $coverImageFile;

        if ($coverImageFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getCoverImageFile(): ?File
    {
        return $this->coverImageFile;
    }

    public function setCoverImageName(?string $coverImageName): void
    {
        $this->coverImageName = $coverImageName;
    }

    public function getCoverImageName(): ?string
    {
        return $this->coverImageName;
    }

    public function __construct()
    {
        $this->exemplaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

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
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaire(Exemplaires $exemplaire): static
    {
        if (!$this->exemplaires->contains($exemplaire)) {
            $this->exemplaires->add($exemplaire);
            $exemplaire->setLivre($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->exemplaires->removeElement($exemplaire)) {
            // set the owning side to null (unless already changed)
            if ($exemplaire->getLivre() === $this) {
                $exemplaire->setLivre(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?categories
    {
        return $this->categorie;
    }

    public function setCategorie(?categories $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getClasse(): ?classes
    {
        return $this->classe;
    }

    public function setClasse(?classes $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

     public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getCoverUrl(): ?string
    {
        return $this->coverUrl;
    }

    public function setCoverUrl(?string $coverUrl): self
    {
        $this->coverUrl = $coverUrl;
        return $this;
    }

    public function getEditionKey(): ?string
    {
        return $this->editionKey;
    }

    public function setEditionKey(?string $editionKey): self
    {
        $this->editionKey = $editionKey;
        return $this;
    }
}
