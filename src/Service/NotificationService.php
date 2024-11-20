<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Envoyer un rappel par e-mail
     */
    public function sendReminder(string $email, string $firstName, \DateTime $endDate): void
    {
        $message = sprintf(
            'Bonjour %s, votre abonnement expirera le %s. Vous pouvez reprendre un nouvel abonnement dès maintenant.',
            $firstName,
            $endDate->format('d-m-Y')
        );

        $emailMessage = (new Email())
            ->from('no-reply@bibliotheque.com')
            ->to($email)
            ->subject('Rappel : expiration de votre abonnement')
            ->text($message);

        $this->mailer->send($emailMessage);
    }
}
