<?php

declare(strict_types=1);

namespace App\Application\Payment;

use App\Domain\Common\Money;

interface PaymentGateway
{
    /**
     * Create a payment intent for the given order amount.
     * Returns a result containing the intent ID and the client secret
     * that must be forwarded to the frontend for payment confirmation.
     */
    public function createIntent(string $orderId, Money $amount): PaymentIntentResult;
}
