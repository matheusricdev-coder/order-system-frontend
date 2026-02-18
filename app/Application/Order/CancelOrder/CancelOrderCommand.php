<?php

declare(strict_types=1);

namespace App\Application\Order\CancelOrder;

final class CancelOrderCommand
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $requesterId,
    ) {}
}
