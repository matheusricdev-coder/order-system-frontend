<?php

declare(strict_types=1);

namespace App\Application\Order\GetOrder;

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Exceptions\UnauthorizedOrderException;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

/**
 * Query-side handler (read model).
 *
 * Uses Eloquent directly for rich, denormalized projections with
 * eager-loaded relations. This avoids rebuilding the domain aggregate
 * just to read data — following the CQRS read/write split principle.
 */
final class GetOrderHandler
{
    public function handle(GetOrderQuery $query): array
    {
        /** @var OrderModel|null $order */
        $order = OrderModel::query()
            ->with('items.product')
            ->find($query->orderId);

        if ($order === null) {
            throw OrderNotFoundException::withId($query->orderId);
        }

        if ((string) $order->user_id !== $query->requesterId) {
            throw UnauthorizedOrderException::notOwner($query->orderId);
        }

        return $this->toReadDTO($order);
    }

    private function toReadDTO(OrderModel $order): array
    {
        $items = $order->items->map(static function (OrderItemModel $item): array {
            $lineTotal = $item->quantity * $item->unit_price_amount;

            return [
                'productId'  => $item->product_id,
                'name'       => $item->product->name ?? null,
                'quantity'   => $item->quantity,
                'unitPrice'  => [
                    'amount'   => $item->unit_price_amount,
                    'currency' => $item->unit_price_currency,
                ],
                'totalPrice' => [
                    'amount'   => $lineTotal,
                    'currency' => $item->unit_price_currency,
                ],
            ];
        })->values()->all();

        $totalAmount = $order->items->sum(static fn($i) => $i->quantity * $i->unit_price_amount);
        $currency    = $order->items->first()?->unit_price_currency ?? 'BRL';

        return [
            'id'        => $order->id,
            'status'    => $order->status,
            'userId'    => $order->user_id,
            'items'     => $items,
            'total'     => ['amount' => $totalAmount, 'currency' => $currency],
            'createdAt' => $order->created_at?->toISOString(),
            'updatedAt' => $order->updated_at?->toISOString(),
        ];
    }
}
