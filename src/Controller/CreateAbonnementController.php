<?php
// src/Controller/CreateAbonnementController.php

namespace App\Controller;

use App\Dto\CreateAbonnementInput;
use App\Entity\Abonnement;
use App\Entity\User;
use App\Entity\TypeAbonnement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAbonnementController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/abonnements/create', name: 'create_abonnement', methods: ['POST'])]
    public function createAbonnement(Request $request)
    {
        // Decode the JSON input data
        $data = json_decode($request->getContent(), true);

        // Create DTO and set data
        $input = new CreateAbonnementInput();
        $input->setUserId($data['userId']);
        $input->setTypeAbonnementId($data['typeAbonnementId']);

        // Validate input data
        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        // Fetch user and typeAbonnement entities
        $user = $this->entityManager->getRepository(User::class)->find($input->getUserId());
        $typeAbonnement = $this->entityManager->getRepository(TypeAbonnement::class)->find($input->getTypeAbonnementId());

        if (!$user || !$typeAbonnement) {
            return new JsonResponse(['message' => 'User or TypeAbonnement not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Create the Abonnement
        $abonnement = new Abonnement();
        $abonnement->setAbonne($user);
        $abonnement->setType($typeAbonnement);
        $abonnement->setDateDebut(new \DateTimeImmutable());

        // Calculate the date_fin based on the typeAbonnement duration (assumes duration is stored in days)
        $dateFin = (new \DateTimeImmutable())->add(new \DateInterval("P{$typeAbonnement->getDureeJours()}D"));
        $abonnement->setDateFin($dateFin);

        // Persist the new Abonnement
        $this->entityManager->persist($abonnement);
        $this->entityManager->flush();

        // Return the created abonnement
        return new JsonResponse([
            'id' => $abonnement->getId(),
            'date_debut' => $abonnement->getDateDebut()->format('Y-m-d H:i:s'),
            'date_fin' => $abonnement->getDateFin()->format('Y-m-d H:i:s'),
        ], JsonResponse::HTTP_CREATED);
    }
}
