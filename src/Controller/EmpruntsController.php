<?php
// src/Controller/BorrowController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Exemplaire;
use App\Entity\Exemplaires;
use App\Service\EmpruntService;
use App\Service\EmpruntsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpruntsController extends AbstractController
{
    private EmpruntsService $empruntService;
    private EntityManagerInterface $entityManager;

    public function __construct(EmpruntsService $empruntService, EntityManagerInterface $entityManager)
    {
        $this->empruntService = $empruntService;
        $this->entityManager = $entityManager;
    }

    #[Route('/borrow/{userId}/{exemplaireId}', name: 'borrow_exemplaire', methods: ['POST'])]
    public function borrow(int $userId, int $exemplaireId): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $exemplaire = $this->entityManager->getRepository(Exemplaires::class)->find($exemplaireId);

        if (!$user || !$exemplaire) {
            return new Response('User or Exemplaire not found.', Response::HTTP_NOT_FOUND);
        }

        try {
            $this->empruntService->emprunterExemplaire($user, $exemplaire);
            return new Response('Emprunt successful!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return new Response('Emprunt failed: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
