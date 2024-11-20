<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Exemplaire;
use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class EmpruntService
{
    private EntityManagerInterface $entityManager;
    private EmpruntRepository $empruntRepository;

    public function __construct(EntityManagerInterface $entityManager, EmpruntRepository $empruntRepository)
    {
        $this->entityManager = $entityManager;
        $this->empruntRepository = $empruntRepository;
    }

    public function peutEmprunterExemplaire(User $user, Exemplaire $exemplaire): bool
    {
        // Vérifie si l'utilisateur peut emprunter jusqu'à trois exemplaires
        $empruntActifs = $this->empruntRepository->findActiveEmpruntByUser($user);

        // $nombreExemplairesEmpruntes = 0;
        // foreach ($empruntActifs as $emprunt) {
        //     $nombreExemplairesEmpruntes += $emprunt->getExemplaires()->count();
        // }

        if ($empruntActifs >= 3) {
            return false;
        }

        // Vérifie si l'utilisateur a un abonnement pour les Books de classe premium
        if ($exemplaire->getBook()->getClasse()->getClasse() === 'premium') {
            return $this->userHasActiveAbonnement($user);
        }

        return true;
    }

    private function userHasActiveAbonnement(User $user): bool
    {
        $abonnementActif = $user->getAbonnement()->filter(function ($abonnement) {
            return $abonnement->getDateFin() > new \DateTime();
        });

        return !$abonnementActif->isEmpty();
    }

   public function emprunterExemplaire(User $user, Exemplaire $exemplaire): void
{
    if ($this->peutEmprunterExemplaire($user, $exemplaire)) {
        $emprunt = new Emprunt();
        $emprunt->setBacked(false);

        // Définir la date de début
        $startAt = new \DateTimeImmutable();
        $emprunt->setStartAt($startAt);

        // Ajouter 1 semaine à la date de début pour définir la date de retour normale
        $normalBackAt = $startAt->modify('+1 week');
        $emprunt->setNormalBackAt($normalBackAt);

        // Associer l'utilisateur et les exemplaires
        $emprunt->setUser($user);
        $emprunt->addExemplaire($exemplaire);

        // Persister et enregistrer dans la base de données
        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();
    } else {
        throw new \Exception('Emprunt impossible: limite atteinte ou abonnement requis pour les Books premium.');
    }
}

}
