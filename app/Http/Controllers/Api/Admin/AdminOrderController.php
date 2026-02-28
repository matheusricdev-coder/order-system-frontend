<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = OrderModel::query()->with(['items']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($userId = $request->query('userId')) {
            $query->where('user_id', $userId);
        }

        $perPage = min((int) $request->query('perPage', 20), 100);
        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $paginator->map(fn (OrderModel $o) => $this->toDto($o))->values(),
            'meta' => [
                'total'       => $paginator->total(),
                'perPage'     => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $order = OrderModel::query()->with(['items'])->findOrFail($id);

        return response()->json(['data' => $this->toDto($order)]);
    }

    private function toDto(OrderModel $o): array
    {
        return [
            'id'              => $o->id,
            'userId'          => $o->user_id,
            'status'          => $o->status,
            'totalAmount'     => $o->total_amount,
            'totalCurrency'   => $o->total_currency,
            'paymentIntentId' => $o->payment_intent_id,
            'items'           => $o->relationLoaded('items')
                ? $o->items->map(fn ($item) => [
                    'productId' => $item->product_id,
                    'quantity'  => $item->quantity,
                    'unitPrice' => $item->unit_price_amount,
                ])->values()->all()
                : [],
            'createdAt' => $o->created_at?->toISOString(),
            'updatedAt' => $o->updated_at?->toISOString(),
        ];
    }
}
