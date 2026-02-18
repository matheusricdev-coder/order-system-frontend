<?php

declare(strict_types=1);

namespace App\Application\Order\GetOrder;

interface GetOrderReadModelRepository
{
    /** @return array<string,mixed> */
    public function getByIdForRequester(string $orderId, string $requesterId): array;
}
