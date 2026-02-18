<?php

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\Order\OrderRepository;
use App\Domain\Common\Money;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\OrderItem;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use DomainException;

final class EloquentOrderRepository implements OrderRepository
{
    public function findById(string $id): Order
    {
        $orderModel = OrderModel::query()->find($id);

        if ($orderModel === null) {
            throw new DomainException('Order not found');
        }

        return $this->hydrate($orderModel);
    }

    public function findByIdForUpdate(string $id): Order
    {
        $orderModel = OrderModel::query()
            ->where('id', $id)
            ->lockForUpdate()
            ->first();

        if ($orderModel === null) {
            throw new DomainException('Order not found');
        }

        return $this->hydrate($orderModel);
    }

    public function save(Order $order): void
    {
        OrderModel::query()->updateOrCreate(
            ['id' => $order->id()],
            [
                'user_id' => $order->userId(),
                'status' => $order->status()->value,
            ]
        );

        OrderItemModel::query()->where('order_id', $order->id())->delete();

        foreach ($order->items() as $item) {
            OrderItemModel::query()->create([
                'id' => $item->id(),
                'order_id' => $order->id(),
                'product_id' => $item->productId(),
                'quantity' => $item->quantity(),
                'unit_price_amount' => $item->unitPrice()->amount(),
                'unit_price_currency' => $item->unitPrice()->currency(),
            ]);
        }
    }

    private function hydrate(OrderModel $orderModel): Order
    {
        $order = new Order(
            id: (string) $orderModel->id,
            userId: (string) $orderModel->user_id
        );

        $items = OrderItemModel::query()->where('order_id', (string) $orderModel->id)->get();

        foreach ($items as $itemModel) {
            $order->addItem(new OrderItem(
                id: (string) $itemModel->id,
                productId: (string) $itemModel->product_id,
                quantity: (int) $itemModel->quantity,
                unitPrice: new Money((int) $itemModel->unit_price_amount, (string) $itemModel->unit_price_currency)
            ));
        }

        $status = OrderStatus::from((string) $orderModel->status);

        if ($status === OrderStatus::PAID) {
            $order->markAsPaid();
        } elseif ($status === OrderStatus::CANCELLED) {
            $order->markAsCancelled();
        }

        return $order;
    }
}
