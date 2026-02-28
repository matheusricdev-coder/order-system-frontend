<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminStockController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $paginator = StockModel::query()
            ->orderBy('product_id')
            ->paginate(50);

        return response()->json([
            'data' => $paginator->map(fn (StockModel $s) => $this->toDto($s))->values(),
            'meta' => [
                'total'       => $paginator->total(),
                'perPage'     => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage'    => $paginator->lastPage(),
            ],
        ]);
    }

    /** Directly set `quantity_total` for a product's stock. */
    public function update(Request $request, string $productId): JsonResponse
    {
        $validated = $request->validate([
            'quantityTotal' => ['required', 'integer', 'min:0'],
        ]);

        /** @var StockModel|null $stock */
        $stock = StockModel::query()->where('product_id', $productId)->firstOrFail();

        $stock->update(['quantity_total' => (int) $validated['quantityTotal']]);

        return response()->json(['data' => $this->toDto($stock)]);
    }

    private function toDto(StockModel $s): array
    {
        return [
            'id'               => $s->id,
            'productId'        => $s->product_id,
            'quantityTotal'    => $s->quantity_total,
            'quantityReserved' => $s->quantity_reserved,
            'quantityAvailable' => $s->quantity_total - $s->quantity_reserved,
            'updatedAt'        => $s->updated_at?->toISOString(),
        ];
    }
}
