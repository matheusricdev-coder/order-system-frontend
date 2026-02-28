<?php

declare(strict_types=1);

namespace App\Application\Order\FailPayment;

final class FailPaymentCommand
{
    public function __construct(
        public readonly string $paymentIntentId,
    ) {}
}
