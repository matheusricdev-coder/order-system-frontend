<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrderReadEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_order_detail(): void
    {
        [$orderId, $userId] = $this->seedOrder();

        $this->withBearerToken($userId)
            ->getJson("/api/v1/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('id', $orderId)
            ->assertJsonPath('userId', $userId)
            ->assertJsonPath('items.0.totalPrice.amount', 2000);
    }

    public function test_it_returns_401_without_auth_on_show(): void
    {
        [$orderId] = $this->seedOrder();

        $this->getJson("/api/v1/orders/{$orderId}")
            ->assertStatus(401);
    }

    public function test_it_returns_403_when_user_does_not_own_order(): void
    {
        [$orderId] = $this->seedOrder();
        $otherId   = (string) str()->uuid();

        UserModel::query()->create([
            'id'         => $otherId,
            'name'       => 'Other',
            'surname'    => 'User',
            'birth_date' => '1990-01-01',
            'password'   => 'secret',
            'active'     => true,
        ]);

        $this->withBearerToken($otherId)
            ->getJson("/api/v1/orders/{$orderId}")
            ->assertStatus(403);
    }

    public function test_it_lists_orders_scoped_to_authenticated_user(): void
    {
        [, $userId] = $this->seedOrder();

        // Seed an order belonging to a different user — must NOT appear in list
        $this->seedOrder();

        $this->withBearerToken($userId)
            ->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_it_returns_401_without_auth_on_list(): void
    {
        $this->getJson('/api/v1/orders')
            ->assertStatus(401);
    }

    public function test_it_returns_404_when_order_does_not_exist(): void
    {
        $userId = (string) str()->uuid();

        UserModel::query()->create([
            'id'         => $userId,
            'name'       => 'Test',
            'surname'    => 'User',
            'birth_date' => '1990-01-01',
            'password'   => 'secret',
            'active'     => true,
        ]);

        $this->withBearerToken($userId)
            ->getJson('/api/v1/orders/' . str()->uuid())
            ->assertStatus(404)
            ->assertJsonPath('error.message', 'Order not found');
    }

    private function seedOrder(): array
    {
        $orderId   = (string) str()->uuid();
        $userId    = (string) str()->uuid();
        $productId = (string) str()->uuid();

        UserModel::query()->create([
            'id'         => $userId,
            'name'       => 'Test',
            'surname'    => 'User',
            'birth_date' => '1990-01-01',
            'password'   => 'secret',
            'active'     => true,
        ]);

        ProductModel::query()->create([
            'id'             => $productId,
            'name'           => 'Teclado',
            'price_amount'   => 1000,
            'price_currency' => 'BRL',
            'category_id'    => (string) str()->uuid(),
            'company_id'     => (string) str()->uuid(),
        ]);

        OrderModel::query()->create([
            'id'      => $orderId,
            'user_id' => $userId,
            'status'  => 'created',
        ]);

        OrderItemModel::query()->create([
            'id'                  => (string) str()->uuid(),
            'order_id'            => $orderId,
            'product_id'          => $productId,
            'quantity'            => 2,
            'unit_price_amount'   => 1000,
            'unit_price_currency' => 'BRL',
        ]);

        return [$orderId, $userId];
    }

    private function withBearerToken(string $userId): static
    {
        $token = UserModel::query()->findOrFail($userId)->createToken('test-token')->plainTextToken;

        return $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
    }
}
