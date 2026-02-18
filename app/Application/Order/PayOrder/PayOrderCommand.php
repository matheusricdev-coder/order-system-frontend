<?php

declare(strict_types=1);

namespace App\Application\Order\PayOrder;

final class PayOrderCommand
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $requesterId,
    ) {}
}
