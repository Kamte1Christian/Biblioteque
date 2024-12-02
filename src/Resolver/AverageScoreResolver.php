<?php

namespace App\Resolver;

use App\Entity\Book;
use App\Entity\Notation;
use Doctrine\ORM\EntityManagerInterface;

class AverageScoreResolver
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(?object $item, array $context): ?object
    {
        $args = $context['args']['input'] ?? [];
        $Bookid = (int) $args['Bookid'] ?? null;

        if (!$Bookid) {
            throw new \Exception('Missing Book ID');
        }

        $book = $this->entityManager->getRepository(Book::class)->find($Bookid);
        if (!$book) {
            throw new \Exception('Book with id ' . $Bookid . ' not found');
        }
     $Averagescore= $book->getAverageScore();

        if ($Averagescore==null) {
        $notations = $this->entityManager->getRepository(Notation::class)->findBy(['Book' => $book]);
        $totalScore = array_reduce($notations, function ($carry, $notation) {
            return $carry + $notation->getScore();
        }, 0);

        $average = $totalScore / count($notations);
        $book->setAveragescore(round($average, 2));
        $this->entityManager->persist($book);
            $this->entityManager->flush();
             return $book;
        }

        return $book->getAverageScore() ?? 'No score found for this book';
    }
}
