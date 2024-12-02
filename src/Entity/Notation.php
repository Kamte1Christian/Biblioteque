<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use App\Repository\NotationRepository;
use App\Resolver\AverageScoreResolver;
use App\Resolver\NotationResolver;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\CorsBundle\Options\Resolver;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotationRepository::class)]
#[ApiResource(
    graphQlOperations:[
        new Query(),
        new Mutation(
            name:"createNotation",
            resolver:NotationResolver::class,
            args:[
                'Bookid'=>[
                    'type'=>'Int!',
                    'description'=>'The id of the book'
                ],
                'Score'=>[
                    'type'=>'Int!',
                    'description'=>'The book\'s rating'
                ]
            ]
        ),
       
    ]
)]
class Notation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    private ?Book $Book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->Book;
    }

    public function setBook(?Book $Book): static
    {
        $this->Book = $Book;

        return $this;
    }
}
