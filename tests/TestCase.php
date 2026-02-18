<?php

namespace Tests;

use App\Models\CategoryModel;
use App\Models\CompanyModel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create (or reuse) a test category and return its ID.
     * Useful in tests that need a valid FK for products.category_id.
     */
    protected function seedCategory(string $id = null, string $name = 'Test Category'): string
    {
        $id = $id ?? (string) str()->uuid();
        CategoryModel::query()->firstOrCreate(['id' => $id], ['name' => $name]);

        return $id;
    }

    /**
     * Create (or reuse) a test company and return its ID.
     * Useful in tests that need a valid FK for products.company_id.
     */
    protected function seedCompany(string $id = null, string $tradeName = 'Test Company'): string
    {
        $id = $id ?? (string) str()->uuid();
        CompanyModel::query()->firstOrCreate(['id' => $id], ['trade_name' => $tradeName]);

        return $id;
    }
}
