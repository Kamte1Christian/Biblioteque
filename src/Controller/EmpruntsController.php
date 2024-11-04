<?php

namespace App\Controller;

use App\Entity\EmpruntExemplaire;
use App\Entity\Emprunts;
use App\Entity\Exemplaires;
use App\Repository\EmpruntExemplaireRepository;
use App\Repository\AbonnementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmpruntsController extends AbstractController
{
    private $em;
    private $empruntExemplaireRepository;
    private $abonnementRepository;

    public function __construct(EntityManagerInterface $em, EmpruntExemplaireRepository $empruntExemplaireRepository, AbonnementsRepository $abonnementRepository)
    {
        $this->em = $em;
        $this->empruntExemplaireRepository = $empruntExemplaireRepository;
        $this->abonnementRepository = $abonnementRepository;
    }

    #[Route('/api/emprunter', name: 'emprunter_exemplaires', methods: ['POST'])]
    public function emprunterExemplaires(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $exemplaireIds = $request->get('exemplaires'); // IDs des exemplaires à emprunter

        // Vérifier le nombre d'exemplaires non retournés pour cet utilisateur
        $exemplairesNonRetournes = $this->empruntExemplaireRepository->countActiveExemplairesForUser($user);

        // Vérifier si l'utilisateur peut emprunter les exemplaires demandés
        if (($exemplairesNonRetournes + count($exemplaireIds)) > 3) {
            return new JsonResponse([
                'message' => 'Vous avez déjà emprunté le nombre maximum de trois exemplaires sans les retourner.'
            ], 400);
        }

        // Vérifier les droits d'abonnement pour les exemplaires demandés
        foreach ($exemplaireIds as $exemplaireId) {
            $exemplaire = $this->em->getRepository(Exemplaires::class)->find($exemplaireId);
            $livreClasse = $exemplaire->getLivre()->getClasseLivre()->getNom();

            // Si le livre est premium, vérifiez l'abonnement actif de l'utilisateur
            if ($livreClasse === 'premium') {
                $abonnementActif = $this->abonnementRepository->findActiveAbonnement($user);
                if (!$abonnementActif || $abonnementActif->getDateFin() < new \DateTime()) {
                    return new JsonResponse([
                        'message' => 'Un abonnement actif est requis pour emprunter des livres premium.'
                    ], 403);
                }
            }
        }

        // Création d'un nouvel emprunt unique pour l'utilisateur
        $emprunt = new Emprunts();
        $emprunt->setUser($user);
        $emprunt->setStartAt(new \DateTimeImmutable()); // Date de l'emprunt
        $this->em->persist($emprunt);

        // Associer chaque exemplaire à l'emprunt unique
        foreach ($exemplaireIds as $exemplaireId) {
            $exemplaire = $this->em->getRepository(Exemplaires::class)->find($exemplaireId);

            // Crée un EmpruntExemplaire pour chaque exemplaire
            $empruntExemplaire = new EmpruntExemplaire();
            $empruntExemplaire->setEmprunt($emprunt);
            $empruntExemplaire->setExemplaire($exemplaire);
            // $empruntExemplaire->setDateEmprunt(new \DateTime());

            $this->em->persist($empruntExemplaire);
        }

        // Enregistrer l'emprunt avec les exemplaires associés
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Emprunt enregistré avec succès !'
        ], 201);
    }
}
