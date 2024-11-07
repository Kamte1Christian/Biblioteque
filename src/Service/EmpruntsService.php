<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Exemplaire;
use App\Entity\Emprunt;
use App\Entity\Emprunts;
use App\Entity\Exemplaires;
use App\Repository\EmpruntRepository;
use App\Repository\EmpruntsRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmpruntsService
{
    private EntityManagerInterface $entityManager;
    private EmpruntsRepository $empruntRepository;

    public function __construct(EntityManagerInterface $entityManager, EmpruntsRepository $empruntRepository)
    {
        $this->entityManager = $entityManager;
        $this->empruntRepository = $empruntRepository;
    }

    public function peutEmprunterExemplaire(User $user, Exemplaires $exemplaire): bool
    {
        // Vérifie si l'utilisateur peut emprunter jusqu'à trois exemplaires
        $empruntActifs = $this->empruntRepository->findActiveEmpruntsByUser($user);

        $nombreExemplairesEmpruntes = 0;
        foreach ($empruntActifs as $emprunt) {
            $nombreExemplairesEmpruntes += $emprunt->getExemplaires()->count();
        }

        if ($nombreExemplairesEmpruntes >= 3) {
            return false;
        }

        // Vérifie si l'utilisateur a un abonnement pour les livres de classe premium
        if ($exemplaire->getLivre()->getClasse()->getClasse() === 'premium') {
            return $this->userHasActiveAbonnement($user);
        }

        return true;
    }

    private function userHasActiveAbonnement(User $user): bool
    {
        $abonnementActif = $user->getAbonnements()->filter(function ($abonnement) {
            return $abonnement->getDateFin() > new \DateTime();
        });

        return !$abonnementActif->isEmpty();
    }

    public function emprunterExemplaire(User $user, Exemplaires $exemplaire): void
    {
        if ($this->peutEmprunterExemplaire($user, $exemplaire)) {
            $emprunt = new Emprunts();
            $emprunt->setUser($user);
            $emprunt->addExemplaire($exemplaire);
            $this->entityManager->persist($emprunt);
            $this->entityManager->flush();
        } else {
            throw new \Exception('Emprunt impossible: limite atteinte ou abonnement requis pour les livres premium.');
        }
    }
}
