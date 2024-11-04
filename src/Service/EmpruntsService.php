<?php

namespace App\Service;

use App\Entity\EmpruntExemplaire;
use App\Entity\Emprunts;
use App\Entity\Exemplaires;
use App\Entity\Livres;
use App\Entity\User;
use App\Repository\AbonnementsRepository;
use App\Repository\EmpruntsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

 class EmpruntsService
 {
//     private $entityManager;
//     private $security;
//     private EmpruntsRepository $empruntRepository;
//     private AbonnementsRepository $abonnementRepository;

//     public function __construct(EmpruntsRepository $empruntRepository, Security $security,  AbonnementsRepository $abonnementRepository, EntityManagerInterface $entityManager)
//     {
//         $this->empruntRepository = $empruntRepository;
//         $this->entityManager = $entityManager;
//         $this->security = $security;
//         $this->abonnementRepository = $abonnementRepository;
//     }

//     public function peutEmprunter(User $user): bool
//     {
//         // Compte les emprunts actifs (statut = 'en cours') pour cet utilisateur
//         $empruntsActifs = $this->empruntRepository->count([
//             'user' => $user,
//             'isBacked' => false
//         ]);

//         // Limite de trois emprunts actifs maximum
//         return $empruntsActifs < 3;
//     }

//     public function emprunterLivre(User $user,Exemplaires $livre): string
//     {
//         if (!$this->peutEmprunter($user)) {
//             return "Vous avez atteint la limite d'emprunt de trois livres. Veuillez retourner un livre avant d'en emprunter un autre.";
//         }

//         // Crée l'emprunt si l'utilisateur peut emprunter
//         $emprunt = new Emprunts();
// $emprunt->setUser($user);
// $emprunt->setStartAt(new \DateTimeImmutable());
// $this->entityManager->persist($emprunt);

// foreach ($exemplaires as $exemplaire) {
//     $empruntExemplaire = new EmpruntExemplaire();
//     $empruntExemplaire->setEmprunt($emprunt);
//     $empruntExemplaire->setExemplaire($exemplaire);
//     $empruntExemplaire->setDateEmprunt(new \DateTime());

//     $this->entityManager->persist($empruntExemplaire);
// }

// $this->entityManager->flush();


//         return "Livre emprunté avec succès.";
//     }

//      public function peutEmprunterLivre(User $user, Exemplaires $livre): bool
//     {
//         // Logique de vérification, comme dans votre méthode
//         if ($livre->getLivre()->getClasse()->getClasse() === 'gratuit') {
//             return true;
//         }

//         $abonnementActif = $this->abonnementRepository->findActiveAbonnement($user);

//         return $abonnementActif !== null && $abonnementActif->getDateFin() > new \DateTime();
//     }

 }
