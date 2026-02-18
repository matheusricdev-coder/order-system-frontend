<?php

declare(strict_types=1);

namespace App\Application\Order\GetOrder;

final class GetOrderHandler
{
    public function __construct(private readonly GetOrderReadModelRepository $readModelRepository)
    {
    }

    public function handle(GetOrderQuery $query): array
    {
        return $this->readModelRepository->getByIdForRequester(
            orderId: $query->orderId,
            requesterId: $query->requesterId,
        );
    }
}
