<?php

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\OrderItem\OrderItem;
use DomainException;

final class Order
{
    private string $id;
    private string $userId;
    private OrderStatus $status;


    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(string $id, string $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->status = OrderStatus::CREATED;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): void
    {
        if ($item->quantity() <= 0) {
            throw new DomainException('Item quantity must be greater than zero');
        }

        $this->items[] = $item;
    }

    public function totalPrice(): Money
    {
        if (empty($this->items)) {
            throw new DomainException('Order must have at least one item');
        }

        $total = null;

        foreach ($this->items as $item) {
            $itemTotal = $item->totalPrice();

            $total = $total === null
                ? $itemTotal
                : new Money(
                    $total->amount() + $itemTotal->amount(),
                    $total->currency()
                );
        }

        return $total;
    }
    public function canBePaid(): bool
    {
        return $this->status === OrderStatus::CREATED;
    }

    public function markAsPaid(): void
    {
        if (!$this->canBePaid()) {
            throw new DomainException('Order cannot be paid');
        }

        $this->status = OrderStatus::PAID;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === OrderStatus::CREATED;
    }

    public function markAsCancelled(): void
    {
        if (!$this->canBeCancelled()) {
            throw new DomainException('Order cannot be cancelled');
        }

        $this->status = OrderStatus::CANCELLED;
    }
}
