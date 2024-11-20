<?php

namespace App\Entity;

use App\Repository\ExemplaireRepository;
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
use ApiPlatform\OpenApi\Model;
use App\Dto\ExemplaireInput;
use App\Resolver\CreateExemplaireResolver;
use App\Resolver\EmpruntResolver;
use App\Resolver\ExemplaireParBookResolver;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExemplaireRepository::class)]
#[ApiResource(
    operations: [
         new Post(
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'Book' => [
                                        'type' => 'string',
                                        'example'=>'/api/books/1'
                                    ],
                                    'code_bar' => [
                                        'type' => 'string',
                                        'example'=>'1234TR'
                                    ],
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            paginationEnabled: false, security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')")
    ],
    graphQlOperations: [
        new Query(),
        new QueryCollection(),
        new QueryCollection(
            name:"collectionParLivre",
            resolver:ExemplaireParBookResolver::class,
            args:[
                'bookId'=>[
                    'type'=>'Int'
                ]
            ]
            ),
        new Mutation(
            name: "createExemplaire",
            resolver:CreateExemplaireResolver::class,
            args:[
                'Bookid'=>[
                    'type'=>'Int'
                ],
                'code'=>[
                    'type'=>'[String]'
                ]
            ]
            ),
        new Mutation(name: "update"),
        new mutation(name: "delete")
    ],
    paginationEnabled:\false,
    security: "is_granted('ROLE_ADMIN')",
    securityMessage: "Access Denied"
)]
class Exemplaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Exemplaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $Book = null;

    #[ORM\Column]
    private ?string $code_bar = null;

    #[ORM\ManyToOne(inversedBy: 'Exemplaire',cascade:['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Emprunt $emprunt = null;

    #[ORM\Column]
    private ?bool $state = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->Book;
    }

    public function setBook(?Book $Book): self
    {
        $this->Book = $Book;

        return $this;
    }

    public function getCodeBar(): ?string
    {
        return $this->code_bar;
    }

    public function setCodeBar(string $code_bar): self
    {
        $this->code_bar = $code_bar;

        return $this;
    }

    public function getEmprunt(): ?Emprunt
    {
        return $this->emprunt;
    }

    public function setEmprunt(?Emprunt $emprunt): self
    {
        $this->emprunt = $emprunt;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = \false;

        return $this;
    }
}
