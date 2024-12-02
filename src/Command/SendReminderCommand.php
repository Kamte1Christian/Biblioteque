<?php

namespace App\Command;

use App\Repository\AbonnementRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendReminderCommand extends Command
{
    // protected static $defaultName = 'app:send-abonnement-reminder';

    // private $abonnementRepository;
    // private $notificationService;

    // public function __construct(AbonnementRepository $abonnementRepository, NotificationService $notificationService)
    // {
    //     parent::__construct();
    //     $this->abonnementRepository = $abonnementRepository;
    //     $this->notificationService = $notificationService;
    // }

    // protected function configure(): void
    // {
    //     $this
    //         ->setDescription('Envoie un rappel aux abonnés dont l\'abonnement expire dans 3 jours.');
    // }

    // protected function execute(InputInterface $input, OutputInterface $output): int
    // {
    //     // Calculer la date dans 3 jours
    //     $dateInThreeDays = (new \DateTime())->modify('+3 days')->format('Y-m-d');

    //     // Récupérer les abonnés dont l'abonnement expire dans 3 jours
    //     $abonnements = $this->abonnementRepository->findByEndDate($dateInThreeDays);

    //     if (empty($abonnements)) {
    //         $output->writeln('Aucun abonné n\'a un abonnement expirant dans 3 jours.');
    //         return Command::SUCCESS;
    //     }

    //     foreach ($abonnements as $abonnement) {
    //         $user = $abonnement->getUser();
    //         $this->notificationService->sendReminder(
    //             $user->getEmail(),
    //             $user->getFname(), // Supposons qu'on utilise le prénom pour personnaliser
    //             $abonnement->getEndDate()
    //         );
    //         $output->writeln("Rappel envoyé à {$user->getEmail()}");
    //     }

    //     return Command::SUCCESS;
    // }
}
