<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\Order\OrderRepository;
use App\Domain\Common\Money;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderStatus;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

final class EloquentOrderRepository implements OrderRepository
{
    public function findById(string $id): Order
    {
        $orderModel = OrderModel::query()->with('items')->find($id);

        if ($orderModel === null) {
            throw OrderNotFoundException::withId($id);
        }

        return $this->hydrate($orderModel);
    }

    public function findByIdForUpdate(string $id): Order
    {
        $orderModel = OrderModel::query()
            ->with('items')
            ->where('id', $id)
            ->lockForUpdate()
            ->first();

        if ($orderModel === null) {
            throw OrderNotFoundException::withId($id);
        }

        return $this->hydrate($orderModel);
    }

    public function findByPaymentIntentId(string $intentId): Order
    {
        $orderModel = OrderModel::query()
            ->with('items')
            ->where('payment_intent_id', $intentId)
            ->first();

        if ($orderModel === null) {
            throw OrderNotFoundException::withPaymentIntentId($intentId);
        }

        return $this->hydrate($orderModel);
    }

    public function save(Order $order): void
    {
        $exists = OrderModel::query()->where('id', $order->id())->exists();

        if ($exists) {
            // Items are immutable after order creation — only update mutable fields.
            OrderModel::query()->where('id', $order->id())->update([
                'status'             => $order->status()->value,
                'payment_intent_id'  => $order->paymentIntentId(),
            ]);
        } else {
            OrderModel::query()->create([
                'id'                => $order->id(),
                'user_id'           => $order->userId(),
                'status'            => $order->status()->value,
                'payment_intent_id' => $order->paymentIntentId(),
            ]);

            foreach ($order->items() as $item) {
                OrderItemModel::query()->create([
                    'id'                  => $item->id(),
                    'order_id'            => $order->id(),
                    'product_id'          => $item->productId(),
                    'quantity'            => $item->quantity(),
                    'unit_price_amount'   => $item->unitPrice()->amount(),
                    'unit_price_currency' => $item->unitPrice()->currency(),
                ]);
            }
        }
    }

    /**
     * Reconstruct the Order aggregate from a persisted model.
     * Uses Order::reconstitute() to bypass business rules and avoid
     * spurious domain events during hydration.
     */
    private function hydrate(OrderModel $orderModel): Order
    {
        $items = ($orderModel->relationLoaded('items') ? $orderModel->items : $orderModel->items()->get())
            ->map(static fn(OrderItemModel $itemModel) => new OrderItem(
                id: (string) $itemModel->id,
                productId: (string) $itemModel->product_id,
                quantity: (int) $itemModel->quantity,
                unitPrice: new Money(
                    (int) $itemModel->unit_price_amount,
                    (string) $itemModel->unit_price_currency,
                ),
            ))
            ->all();

        return Order::reconstitute(
            id: (string) $orderModel->id,
            userId: (string) $orderModel->user_id,
            status: OrderStatus::from((string) $orderModel->status),
            items: $items,
            paymentIntentId: $orderModel->payment_intent_id ? (string) $orderModel->payment_intent_id : null,
        );
    }
}
