<?php

declare(strict_types=1);

namespace App\Application\Order\ListOrders;

final class ListOrdersHandler
{
    public function __construct(private readonly ListOrdersReadModelRepository $readModelRepository)
    {
    }

    public function handle(ListOrdersQuery $query): array
    {
        return $this->readModelRepository->listForRequester(
            requesterId: $query->requesterId,
            status: $query->status,
            perPage: $query->perPage,
            page: $query->page,
        );
    }
}
