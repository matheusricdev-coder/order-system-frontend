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
        public readonly ?string $paymentIntentId = null,
        public readonly ?string $clientSecret = null,
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
            paymentIntentId: $order->paymentIntentId(),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id'              => $this->id,
            'userId'          => $this->userId,
            'status'          => $this->status,
            'total'           => [
                'amount'   => $this->totalAmountInCents,
                'currency' => $this->currency,
            ],
            'items'           => array_map(
                static fn(OrderItemDTO $item) => $item->toArray(),
                $this->items,
            ),
            // Only present when a payment is being initiated — consumed by the frontend
            // to call stripe.confirmPayment(). Not stored server-side.
            'clientSecret'    => $this->clientSecret,
            'paymentIntentId' => $this->paymentIntentId,
        ], static fn (mixed $v) => $v !== null);
    }
}
