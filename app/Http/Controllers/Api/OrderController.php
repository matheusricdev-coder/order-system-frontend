<?php

namespace App\Http\Controllers\Api;

use App\Application\Order\CancelOrder\CancelOrderHandler;
use App\Application\Order\CreateOrder\CreateOrderCommand;
use App\Application\Order\CreateOrder\CreateOrderHandler;
use App\Application\Order\PayOrder\PayOrderHandler;
use App\Domain\Order\Order;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Requests\CreateOrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class OrderController extends Controller
{
    public function create(CreateOrderRequest $request, CreateOrderHandler $handler): JsonResponse
    {
        $payload = $request->validated();

        $order = $handler->handle(new CreateOrderCommand(
            userId: $payload['userId'],
            items: $payload['items'],
        ));

        foreach ($order->items() as $item) {
            Log::info('order.created.item_reserved', [
                'order_id' => $order->id(),
                'user_id' => $order->userId(),
                'product_id' => $item->productId(),
                'quantity' => $item->quantity(),
                'status_from' => null,
                'status_to' => $order->status()->value,
            ]);
        }

        return response()->json($this->serializeOrder($order, $request), 201);
    }

    public function pay(string $id, Request $request, PayOrderHandler $handler): JsonResponse
    {
        $order = $handler->handle($id);

        foreach ($order->items() as $item) {
            Log::info('order.paid.item_consumed', [
                'order_id' => $order->id(),
                'user_id' => $order->userId(),
                'product_id' => $item->productId(),
                'quantity' => $item->quantity(),
                'status_from' => 'created',
                'status_to' => $order->status()->value,
            ]);
        }

        return response()->json($this->serializeOrder($order, $request));
    }

    public function cancel(string $id, Request $request, CancelOrderHandler $handler): JsonResponse
    {
        $handler->handle($id);

        Log::info('order.cancelled', [
            'order_id' => $id,
            'user_id' => null,
            'product_id' => null,
            'quantity' => null,
            'status_from' => 'created',
            'status_to' => 'cancelled',
        ]);

        return response()->json([
            'orderId' => $id,
            'status' => 'cancelled',
            'correlationId' => $this->correlationId($request),
        ]);
    }

    private function serializeOrder(Order $order, Request $request): array
    {
        $total = $order->totalPrice();

        return [
            'orderId' => $order->id(),
            'status' => $order->status()->value,
            'total' => [
                'amount' => $total->amount(),
                'currency' => $total->currency(),
            ],
            'items' => array_map(static fn ($item): array => [
                'productId' => $item->productId(),
                'quantity' => $item->quantity(),
                'unitPrice' => [
                    'amount' => $item->unitPrice()->amount(),
                    'currency' => $item->unitPrice()->currency(),
                ],
            ], $order->items()),
            'correlationId' => $this->correlationId($request),
        ];
    }

    private function correlationId(Request $request): ?string
    {
        return $request->attributes->get(CorrelationIdMiddleware::ATTRIBUTE);
    }
}
