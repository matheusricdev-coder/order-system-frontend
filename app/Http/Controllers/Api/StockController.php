<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\StockModel;
use Illuminate\Http\JsonResponse;

final class StockController extends Controller
{
    public function show(string $productId): JsonResponse
    {
        ProductModel::query()->findOrFail($productId);
        $stock = StockModel::query()->where('product_id', $productId)->firstOrFail();

        return response()->json($this->toDto($stock));
    }

    public function showByProduct(string $id): JsonResponse
    {
        return $this->show($id);
    }

    private function toDto(StockModel $stock): array
    {
        return [
            'productId' => $stock->product_id,
            'quantityTotal' => $stock->quantity_total,
            'quantityReserved' => $stock->quantity_reserved,
            'quantityAvailable' => $stock->quantity_total - $stock->quantity_reserved,
        ];
    }
}
