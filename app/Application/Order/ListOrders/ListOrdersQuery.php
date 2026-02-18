<?php

declare(strict_types=1);

namespace App\Application\Order\ListOrders;

final class ListOrdersQuery
{
    public function __construct(
        public readonly string $requesterId,
        public readonly ?string $status = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}
}
