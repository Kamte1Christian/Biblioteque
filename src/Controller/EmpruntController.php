<?php
// src/Controller/BorrowController.php
namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\CreateAbonnementInput;
use App\Dto\CreateEmpruntInput;
use App\Entity\User;
use App\Entity\Exemplaire;
use App\Service\EmpruntService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class EmpruntController extends AbstractController
{
    private EmpruntService $empruntService;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;


    public function __construct(EmpruntService $empruntService, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->empruntService = $empruntService;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/emprunter/create', name: 'create_emprunt', methods: ['POST'])]
    public function borrow( Request $request): Response
    {
        // Decode the JSON input data
        $data = json_decode($request->getContent(), true);

        // Create DTO and set data
        $input = new CreateEmpruntInput();
        $input->setUserId($data['userId']);
        $input->setExemplaireId($data['exemplaireId']);

        // Validate input data
       $errors = $this->validator->validate($input);

if ($errors instanceof ConstraintViolationListInterface && count($errors) > 0) {
    return new JsonResponse((string)$errors, JsonResponse::HTTP_BAD_REQUEST);
}

        // Fetch user and typeAbonnement entities
        $user = $this->entityManager->getRepository(User::class)->find($input->getUserId());
        $exemplaire = $this->entityManager->getRepository(Exemplaire::class)->find($input->getExemplaireId());

        if (!$user || !$exemplaire) {
            return new JsonResponse(['message' => 'User or TypeAbonnement not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $this->empruntService->emprunterExemplaire($user, $exemplaire);
            return new Response('Emprunt successful!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return new Response('Emprunt failed: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
