<?php

declare(strict_types=1);

namespace App\Application\Order\FinalizePayment;

final class FinalizePaymentCommand
{
    public function __construct(
        public readonly string $paymentIntentId,
    ) {}
}
