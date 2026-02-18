<?php

declare(strict_types=1);

namespace App\Application\Order\GetOrder;

final class GetOrderQuery
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $requesterId,
    ) {}
}
