<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Order\Events\OrderCancelled;
use App\Domain\Order\Events\OrderCreated;
use App\Domain\Order\Events\OrderPaid;
use App\Domain\Order\Exceptions\InvalidOrderTransitionException;

final class Order
{
    private string $id;
    private string $userId;
    private OrderStatus $status;

    /** @var OrderItem[] */
    private array $items = [];

    /** @var object[] */
    private array $domainEvents = [];

    public function __construct(string $id, string $userId)
    {
        $this->id     = $id;
        $this->userId = $userId;
        $this->status = OrderStatus::CREATED;
    }

    /**
     * Reconstruct an Order from persistence without triggering business rules
     * or recording domain events (bypasses transition guards).
     *
     * @param OrderItem[] $items
     */
    public static function reconstitute(
        string $id,
        string $userId,
        OrderStatus $status,
        array $items,
    ): self {
        $order         = new self($id, $userId);
        $order->status = $status;
        $order->items  = $items;

        return $order;
    }

    public function id(): string { return $this->id; }
    public function userId(): string { return $this->userId; }
    public function items(): array { return $this->items; }
    public function status(): OrderStatus { return $this->status; }

    public function ownedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }

    public function addItem(OrderItem $item): void
    {
        if ($item->quantity() <= 0) {
            throw new \DomainException('Item quantity must be greater than zero');
        }

        $this->items[] = $item;
    }

    public function totalPrice(): Money
    {
        if (empty($this->items)) {
            throw new \DomainException('Order must have at least one item');
        }

        $total = null;
        foreach ($this->items as $item) {
            $total = $total === null ? $item->totalPrice() : $total->add($item->totalPrice());
        }

        return $total;
    }

    public function canBePaid(): bool
    {
        return $this->status->canTransitionTo(OrderStatus::PAID);
    }

    public function markAsPaid(): void
    {
        if (!$this->canBePaid()) {
            throw InvalidOrderTransitionException::cannotBePaid($this->status->value);
        }

        $this->status = OrderStatus::PAID;
        $this->recordEvent(new OrderPaid($this->id, $this->userId, new \DateTimeImmutable()));
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canTransitionTo(OrderStatus::CANCELLED);
    }

    public function markAsCancelled(): void
    {
        if (!$this->canBeCancelled()) {
            throw InvalidOrderTransitionException::cannotBeCancelled($this->status->value);
        }

        $this->status = OrderStatus::CANCELLED;
        $this->recordEvent(new OrderCancelled($this->id, $this->userId, new \DateTimeImmutable()));
    }

    /**
     * Record that the order was created (called by handler after all items are added).
     */
    public function recordCreated(): void
    {
        $total = $this->totalPrice();
        $this->recordEvent(new OrderCreated(
            orderId: $this->id,
            userId: $this->userId,
            totalAmountInCents: $total->amount(),
            currency: $total->currency(),
            occurredAt: new \DateTimeImmutable(),
        ));
    }

    /**
     * Pull and clear all pending domain events.
     *
     * @return object[]
     */
    public function pullDomainEvents(): array
    {
        $events            = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
