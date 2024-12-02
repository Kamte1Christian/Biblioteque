<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    private string $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createPaymentIntent(float $amount): PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => $amount * 100, // Montant en cents
                'currency' => 'usd',
            ]);
        } catch (ApiErrorException $e) {
            throw new \Exception("Erreur lors de la création du paiement : " . $e->getMessage());
        }
    }

    public function verifyPayment(string $paymentIntentId): bool
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent->status === 'succeeded'; // Vérification du succès
        } catch (ApiErrorException $e) {
            throw new \Exception("Erreur lors de la vérification du paiement : " . $e->getMessage());
        }
    }
}

