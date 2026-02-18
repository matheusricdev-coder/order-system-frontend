<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\CategoryModel;
use App\Models\CompanyModel;
use App\Models\ProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CatalogEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_and_filters_products(): void
    {
        $categoryId = (string) str()->uuid();
        $companyId = (string) str()->uuid();

        CategoryModel::query()->create(['id' => $categoryId, 'name' => 'Bebidas']);
        CompanyModel::query()->create(['id' => $companyId, 'trade_name' => 'Loja XPTO']);

        ProductModel::query()->create([
            'id' => (string) str()->uuid(),
            'name' => 'Café premium',
            'price_amount' => 1599,
            'price_currency' => 'BRL',
            'category_id' => $categoryId,
            'company_id' => $companyId,
        ]);

        ProductModel::query()->create([
            'id' => (string) str()->uuid(),
            'name' => 'Pão',
            'price_amount' => 500,
            'price_currency' => 'BRL',
            'category_id' => (string) str()->uuid(),
            'company_id' => (string) str()->uuid(),
        ]);

        $response = $this->getJson("/api/v1/products?categoryId={$categoryId}&companyId={$companyId}&q=Café");

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Café premium')
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_categories(): void
    {
        CategoryModel::query()->create(['id' => (string) str()->uuid(), 'name' => 'A']);
        CategoryModel::query()->create(['id' => (string) str()->uuid(), 'name' => 'B']);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(2);
    }
}
