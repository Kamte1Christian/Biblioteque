<?php

namespace App\Resolver;

use App\Dto\CreateAbonnementInput;
use App\Entity\Abonnement;
use App\Entity\TypeAbonnement;
use App\Repository\TypeAbonnementRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAbonnementResolver
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
        $typeAbonnementId = (int)$args['type'];

        // Fetch the TypeAbonnement entity
        $typeAbonnementRepository = $this->entityManager->getRepository(TypeAbonnement::class);
        $type = $typeAbonnementRepository->find($typeAbonnementId);

        if (!$type) {
            throw new \Exception('TypeAbonnement not found');
        }

        // Create and populate the Abonnement entity
        $abonnement = new Abonnement();
        $abonnement->setAbonne($user); // Set the authenticated user as the abonné
        $abonnement->setType($type);
        $abonnement->setDateDebut(new DateTimeImmutable());

        // Calculate the date_fin based on the TypeAbonnement duration
        $dateFin = (new DateTimeImmutable())->add(new \DateInterval("P{$type->getDureeJours()}D"));
        $abonnement->setDateFin($dateFin);

        // Persist the new abonnement
        $this->entityManager->persist($abonnement);
        $this->entityManager->flush();

        return $abonnement;
    }
}
