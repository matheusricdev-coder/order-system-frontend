<?php

declare(strict_types=1);

namespace App\Domain\Order\Events;

final class OrderCreated
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $userId,
        public readonly int $totalAmountInCents,
        public readonly string $currency,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}
