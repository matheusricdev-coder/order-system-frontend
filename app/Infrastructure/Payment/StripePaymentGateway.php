<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

use App\Application\Payment\PaymentGateway;
use App\Application\Payment\PaymentIntentResult;
use App\Domain\Common\Money;
use Stripe\StripeClient;

final class StripePaymentGateway implements PaymentGateway
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient((string) config('services.stripe.secret'));
    }

    public function createIntent(string $orderId, Money $amount): PaymentIntentResult
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount'   => $amount->amount(),   // already in cents
            'currency' => strtolower($amount->currency()),
            'metadata' => ['order_id' => $orderId],
            // Automatic payment methods — lets Stripe decide the best UI
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return new PaymentIntentResult(
            intentId:     $intent->id,
            clientSecret: $intent->client_secret,
        );
    }
}
