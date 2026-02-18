<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;

final class OrderDTO
{
    /** @param OrderItemDTO[] $items */
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $status,
        public readonly int $totalAmountInCents,
        public readonly string $currency,
        public readonly array $items,
    ) {}

    public static function fromDomain(Order $order): self
    {
        $total = $order->totalPrice();

        return new self(
            id: $order->id(),
            userId: $order->userId(),
            status: $order->status()->value,
            totalAmountInCents: $total->amount(),
            currency: $total->currency(),
            items: array_map(
                static fn(OrderItem $item) => OrderItemDTO::fromDomain($item),
                $order->items(),
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'userId'   => $this->userId,
            'status'   => $this->status,
            'total'    => [
                'amount'   => $this->totalAmountInCents,
                'currency' => $this->currency,
            ],
            'items'    => array_map(
                static fn(OrderItemDTO $item) => $item->toArray(),
                $this->items,
            ),
        ];
    }
}
