<?php

declare(strict_types=1);

namespace App\Application\Order\ListOrders;

interface ListOrdersReadModelRepository
{
    /** @return array<string,mixed> */
    public function listForRequester(
        string $requesterId,
        ?string $status,
        int $perPage,
        int $page,
    ): array;
}
