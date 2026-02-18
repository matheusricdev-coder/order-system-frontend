<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\StockModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelOrderEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_cancels_order_and_returns_payload(): void
    {
        $orderId = $this->seedCreatedOrder();

        $response = $this->postJson("/api/orders/{$orderId}/cancel", [], ['X-Correlation-Id' => 'corr-123']);

        $response
            ->assertOk()
            ->assertHeader('X-Correlation-Id', 'corr-123')
            ->assertJsonPath('orderId', $orderId)
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonPath('correlationId', 'corr-123');
    }

    public function test_it_returns_404_when_order_does_not_exist(): void
    {
        $response = $this->postJson('/api/orders/'.str()->uuid().'/cancel');

        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'Order not found');
    }

    public function test_it_returns_409_when_order_cannot_be_cancelled(): void
    {
        $orderId = $this->seedCreatedOrder('paid');

        $response = $this->postJson("/api/orders/{$orderId}/cancel");

        $response
            ->assertStatus(409)
            ->assertJsonPath('message', 'Order cannot be cancelled');
    }

    private function seedCreatedOrder(string $status = 'created'): string
    {
        $orderId = (string) str()->uuid();
        $productId = (string) str()->uuid();

        OrderModel::query()->create([
            'id' => $orderId,
            'user_id' => (string) str()->uuid(),
            'status' => $status,
        ]);

        OrderItemModel::query()->create([
            'id' => (string) str()->uuid(),
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => 1,
            'unit_price_amount' => 1000,
            'unit_price_currency' => 'BRL',
        ]);

        StockModel::query()->create([
            'id' => (string) str()->uuid(),
            'product_id' => $productId,
            'quantity_total' => 10,
            'quantity_reserved' => 1,
        ]);

        return $orderId;
    }
}
