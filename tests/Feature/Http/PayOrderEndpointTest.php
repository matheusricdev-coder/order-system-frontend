<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\StockModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PayOrderEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_pays_order_and_returns_payload(): void
    {
        [$orderId, $productId] = $this->seedCreatedOrder();

        $response = $this->postJson("/api/v1/orders/{$orderId}/pay");

        $response
            ->assertOk()
            ->assertHeader('X-Correlation-Id')
            ->assertJsonPath('orderId', $orderId)
            ->assertJsonPath('status', 'paid');
    }

    public function test_it_returns_404_when_order_does_not_exist(): void
    {
        $response = $this->postJson('/api/v1/orders/'.str()->uuid().'/pay');

        $response
            ->assertStatus(404)
            ->assertJsonPath('error.message', 'Order not found');
    }

    public function test_it_returns_409_when_order_cannot_be_paid(): void
    {
        [$orderId] = $this->seedCreatedOrder('paid');

        $response = $this->postJson("/api/v1/orders/{$orderId}/pay");

        $response
            ->assertStatus(409)
            ->assertJsonPath('error.message', 'Order cannot be paid');
    }

    private function seedCreatedOrder(string $status = 'created'): array
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

        return [$orderId, $productId];
    }
}
