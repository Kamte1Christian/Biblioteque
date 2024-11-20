<?php

namespace App\Resolver;

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\Book;
use App\Repository\ExemplaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Traversable;

class ExemplaireParBookResolver implements QueryCollectionResolverInterface
{
    private ExemplaireRepository $exemplaireRepository;
    private EntityManagerInterface $em;

    public function __construct(ExemplaireRepository $exemplaireRepository,EntityManagerInterface $em)
    {
        $this->exemplaireRepository = $exemplaireRepository;
        $this->em = $em;
    }

 public function __invoke(Traversable|array $collection, array $context): Traversable|array
    {
        $bookId = $context['args']['bookId'] ?? null;

        if (!$bookId) {
            throw new \Exception('Book ID is required.');
        }
        $book= $this->em->getRepository(Book::class)->findOneBy(['id'=>$bookId]);

        return $this->exemplaireRepository->findBy(['Book' => $book]);
    }

    public function countExemplaire(int $bookId): int
    {
        $book= $this->em->getRepository(Book::class)->findOneBy(['id'=>$bookId]);
        return $this->exemplaireRepository->count(['Book' => $book]);
    }
}
