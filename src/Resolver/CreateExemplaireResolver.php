<?php

namespace App\Resolver;

use App\Entity\Exemplaire;
use App\Entity\Book;
use App\Repository\TypeAbonnementRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateExemplaireResolver
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->security = $security;
    }

    public function __invoke(?object $item, array $context): ?object
    {
        // Retrieve the authenticated user
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Extract the input arguments
        $args = $context['args']['input'] ?? [];
        $Bookid = (int) $args['Bookid'] ?? [];
        $codes = (array) $args['code'] ?? [];

 if ( empty($Bookid)) {
            throw new \Exception('Invalid or missing Book');
        }

                $Book = $this->entityManager->getRepository(Book::class)->findOneBy(['id'=>$Bookid]);

                if (!$Book) {
                    throw new \Exception("Book with ID {$Bookid} not found.");
                }



   foreach($codes as $code)  {
    $existExemplaire= $this->entityManager->getRepository(Exemplaire::class)->findOneBy(['code_bar'=>$code]);
    if ($existExemplaire) {
                    throw new \Exception("Exemplaire with codebar {$code} already exists.");
                }
                // Create and populate the Exemplaire entity
        $Exemplaire = new Exemplaire();
        $Exemplaire->setBook($Book); // Set the authenticated user as the abonné
        $Exemplaire->setCodeBar($code);



        // Persist the new Exemplaire
        $this->entityManager->persist($Exemplaire);
        $this->entityManager->flush();
}
        return $Exemplaire;
    }
}
