<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyModel;
use App\Models\ProductModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CompanyController extends Controller
{
    public function show(string $id): JsonResponse
    {
        $company = CompanyModel::query()->findOrFail($id);

        return response()->json([
            'id' => $company->id,
            'tradeName' => $company->trade_name,
        ]);
    }

    public function products(string $id, Request $request): JsonResponse
    {
        CompanyModel::query()->findOrFail($id);

        $products = ProductModel::query()
            ->where('company_id', $id)
            ->orderBy('name')
            ->paginate((int) $request->query('perPage', 15));

        return response()->json($products->through(static fn (ProductModel $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'categoryId' => $product->category_id,
            'companyId' => $product->company_id,
            'price' => [
                'amount' => $product->price_amount,
                'currency' => $product->price_currency,
            ],
        ]));
    }
}
