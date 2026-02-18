<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthAndStockEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authenticates_and_returns_me(): void
    {
        $userId = (string) str()->uuid();

        UserModel::query()->create([
            'id' => $userId,
            'name' => 'Ana',
            'surname' => 'Silva',
            'birth_date' => '1990-01-01',
            'email' => 'ana@example.com',
            'password' => Hash::make('secret123'),
            'active' => true,
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'ana@example.com',
            'password' => 'secret123',
        ])->assertOk();

        $token = $login->json('accessToken');

        $this->getJson('/api/v1/me', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('id', $userId);
    }

    public function test_it_returns_stock_by_product(): void
    {
        $productId = (string) str()->uuid();

        ProductModel::query()->create([
            'id' => $productId,
            'name' => 'Mouse',
            'price_amount' => 500,
            'price_currency' => 'BRL',
            'category_id' => (string) str()->uuid(),
            'company_id' => (string) str()->uuid(),
        ]);

        StockModel::query()->create([
            'id' => (string) str()->uuid(),
            'product_id' => $productId,
            'quantity_total' => 10,
            'quantity_reserved' => 3,
        ]);

        $this->getJson("/api/v1/stocks/{$productId}")
            ->assertOk()
            ->assertJsonPath('quantityAvailable', 7);
    }
}
