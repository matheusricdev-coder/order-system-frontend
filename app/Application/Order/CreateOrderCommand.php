<?php

namespace App\Application\Order\CreateOrder;

final class CreateOrderCommand
{
    private string $userId;

    /** @var array<array{productId: string, quantity: int}> */
    private array $items;

    public function __construct(string $userId, array $items)
    {
        $this->userId = $userId;
        $this->items = $items;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    /**
     * @return array<array{productId: string, quantity: int}>
     */
    public function items(): array
    {
        return $this->items;
    }
}
