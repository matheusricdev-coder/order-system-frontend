<?php

declare(strict_types=1);

namespace App\Domain\Order\Events;

final class OrderPaid
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $userId,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}
