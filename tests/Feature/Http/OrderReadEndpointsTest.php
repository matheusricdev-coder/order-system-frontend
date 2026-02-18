<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrderReadEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_order_detail(): void
    {
        [$orderId, $userId] = $this->seedOrder();

        $this->getJson("/api/v1/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('id', $orderId)
            ->assertJsonPath('userId', $userId)
            ->assertJsonPath('items.0.totalPrice.amount', 2000);
    }

    public function test_it_lists_orders_by_user(): void
    {
        [, $userId] = $this->seedOrder();

        $this->getJson("/api/v1/orders?userId={$userId}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    private function seedOrder(): array
    {
        $orderId = (string) str()->uuid();
        $userId = (string) str()->uuid();
        $productId = (string) str()->uuid();

        ProductModel::query()->create([
            'id' => $productId,
            'name' => 'Teclado',
            'price_amount' => 1000,
            'price_currency' => 'BRL',
            'category_id' => (string) str()->uuid(),
            'company_id' => (string) str()->uuid(),
        ]);

        OrderModel::query()->create([
            'id' => $orderId,
            'user_id' => $userId,
            'status' => 'created',
        ]);

        OrderItemModel::query()->create([
            'id' => (string) str()->uuid(),
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => 2,
            'unit_price_amount' => 1000,
            'unit_price_currency' => 'BRL',
        ]);

        return [$orderId, $userId];
    }
}
