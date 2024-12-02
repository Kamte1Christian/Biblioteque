<?php

namespace App\Resolver;

use App\Entity\Book;
use App\Entity\Notation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class NotationResolver
{

   private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

      public function __invoke(?object $item, array $context): ?object
      {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }


         $args = $context['args']['input'] ?? [];
        $Bookid = (int) $args['Bookid'] ?? [];
        $Score = (int) $args['Score'] ?? [];
        if (!$Bookid || !$Score) {
            throw new \Exception('missing Book or empty score');
        }
         $Book=$this->entityManager->getRepository(Book::class)->findOneBy(['id'=>$Bookid]);
         if (!$Book) {
            throw new \Exception('Book with id'.$Bookid.'not found');
        }
         $existnote= $this->entityManager->getRepository(Notation::class)->findBy(['User'=>$user,'Book'=>$Book]);
        if ($existnote) {
            throw new \Exception('You noted this book Already');
        }
         if ($Score>5) {
            throw new \Exception('Veuillez entrer un score compris entre 0 et 5');
        }


        $notation= new Notation;
        $notation->setBook($Book);
        $notation->setUser($user);
        $notation->setScore($Score);

        $this->entityManager->persist($notation);

       $notations = $this->entityManager->getRepository(Notation::class)->findBy(['Book' => $Book]);

        if (empty($notations)) {
            $this->entityManager->flush();
             return $notation;
        }

        $totalScore = array_reduce($notations, function ($carry, $notation) {
            return $carry + $notation->getScore();
        }, 0);

        $average = $totalScore / count($notations);
        $Book->setAveragescore(round($average, 2));
        $this->entityManager->persist($Book);

        $this->entityManager->flush();

        return $notation;
      }
}

