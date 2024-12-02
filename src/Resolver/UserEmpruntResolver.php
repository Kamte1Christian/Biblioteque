<?php

namespace App\Resolver;

use App\Entity\Emprunt;
use App\Entity\Exemplaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Book;
use App\Entity\User;

class UserEmpruntResolver implements MutationResolverInterface
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
        // Get the authenticated user
      $admin = $this->security->getUser();
        if (!$admin) {
            throw new \Exception('user not authenticated');
        }
         if (!in_array('ROLE_ADMIN', $admin->getRoles(), true)) {

            throw new \InvalidArgumentException('Restricted root, Access denied.');
        }

        // Retrieve the exemplaire IRIs from the GraphQL arguments
        $args = $context['args']['input'] ?? [];
        $userid = (int) $args['Userid'] ?? [];
        $Bookid = (array) $args['Bookid'] ?? [];
        if ((!is_array($Bookid) || empty($Bookid)) || empty($userid)) {
            throw new \Exception('Invalid or missing Book');
        }

          $user = $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$userid]);
          if (!$user) {
            throw new \Exception('User with id '.$userid.' does not exist');
        }
        // Fetch the Exemplaire entities from their book id
        $exemplaires = [];
        foreach ($Bookid as $id) {
                 $Book = $this->entityManager->getRepository(Book::class)->findOneBy(['id'=>$id]);
                $exemplaire = $this->entityManager->getRepository(Exemplaire::class)->findOneBy(['Book'=>$Book,'emprunt'=>null]);

                if (!$exemplaire) {
                    throw new \Exception("There is no exemplaire available for this book");
                }
                $exemplaires[] = $exemplaire;

        }

        // Fetch the user's active Emprunt (if any)
        $existingEmprunt = $this->entityManager->getRepository(Emprunt::class)
            ->findOneBy(['user' => $user, 'isBacked' => false]);

        $alreadyBorrowedCount = 0;
        if ($existingEmprunt) {
            $alreadyBorrowedCount = count($existingEmprunt->getExemplaire());
        }

        $requestedCount = count($exemplaires);
        $remainingBorrowLimit = 3 - $alreadyBorrowedCount;

        if ($requestedCount > $remainingBorrowLimit && $remainingBorrowLimit!=0) {
            throw new \Exception(
                "You can borrow a maximum of 3 exemplaires. You have already borrowed {$alreadyBorrowedCount}, so you can only borrow {$remainingBorrowLimit} more."
            );
        } else if ($remainingBorrowLimit==0) {
            throw new \Exception(
                "Sorry you have reached your borrowing limit."
            );
        }

        // Check if any of the requested Exemplaires are already borrowed
        foreach ($exemplaires as $exemplaire) {
            if ($exemplaire->getEmprunt() !== null) {
                throw new \Exception("Exemplaire with ID {$exemplaire->getId()} is already borrowed.");
            }
        }

        // If the user has an existing Emprunt, add the new Exemplaires to it
        if ($existingEmprunt) {
            foreach ($exemplaires as $exemplaire) {
                $existingEmprunt->addExemplaire($exemplaire);
                $exemplaire->setEmprunt($existingEmprunt); // Update the relationship
            }
        } else {
            // Otherwise, create a new Emprunt
            $existingEmprunt = new Emprunt();
            $existingEmprunt->setUser($user);
            $existingEmprunt->setStartAt(new \DateTimeImmutable());
            $existingEmprunt->setNormalBackAt((new \DateTimeImmutable())->modify('+1 week'));
            $existingEmprunt->setBacked(false);

            foreach ($exemplaires as $exemplaire) {
                $existingEmprunt->addExemplaire($exemplaire);
                $exemplaire->setEmprunt($existingEmprunt); // Update the relationship
            }

            $this->entityManager->persist($existingEmprunt);
        }

        // Persist the Exemplaires
        foreach ($exemplaires as $exemplaire) {
            $this->entityManager->persist($exemplaire);
        }

        // Save changes
        $this->entityManager->flush();

        return $existingEmprunt;
    }
}
