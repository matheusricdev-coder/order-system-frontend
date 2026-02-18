<?php

declare(strict_types=1);

namespace App\Application\Order\ListOrders;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

/**
 * Query-side handler (read model).
 *
 * Returns paginated order list scoped to the authenticated user.
 * Uses Eloquent directly — no domain aggregate rebuilding needed for reads.
 */
final class ListOrdersHandler
{
    public function handle(ListOrdersQuery $query): array
    {
        $result = OrderModel::query()
            ->with('items.product')
            ->where('user_id', $query->requesterId)
            ->when(
                $query->status !== null,
                static fn($q) => $q->where('status', $query->status)
            )
            ->orderByDesc('created_at')
            ->paginate(perPage: $query->perPage, page: $query->page);

        return $result->through(fn(OrderModel $order) => $this->toReadDTO($order))->toArray();
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
