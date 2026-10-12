<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createCheckoutSession(
        array $cart,
        string $successUrl,
        string $cancelUrl
    ): Session {
        $lineItems = [];

        foreach ($cart['items'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product']->getName()
                            . ' - Taille '
                            . $item['size'],
                    ],
                    'unit_amount' => (int) round(
                        ((float) $item['product']->getPrice()) * 100
                    ),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        return $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function retrieveCheckoutSession(string $sessionId): Session
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }
}
