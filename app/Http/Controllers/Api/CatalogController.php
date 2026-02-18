<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ProductModel::query();

        if ($categoryId = $request->query('categoryId')) {
            $query->where('category_id', $categoryId);
        }

        if ($companyId = $request->query('companyId')) {
            $query->where('company_id', $companyId);
        }

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $paginator = $query
            ->with('gallery')
            ->orderBy('name')
            ->paginate((int) $request->query('perPage', 15))
            ->through(fn (ProductModel $product): array => $this->toProductDto($product));

        return response()->json([
            'data' => $paginator->items(),
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
        $product = ProductModel::query()->with('gallery')->findOrFail($id);

        return response()->json(['data' => $this->toProductDto($product)]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => CategoryModel::query()
                ->orderBy('name')
                ->get()
                ->map(static fn (CategoryModel $category): array => [
                    'id'   => $category->id,
                    'name' => $category->name,
                ])
                ->values(),
        ]);
    }

    private function toProductDto(ProductModel $product): array
    {
        return [
            'id'         => $product->id,
            'name'       => $product->name,
            'categoryId' => $product->category_id,
            'companyId'  => $product->company_id,
            'price'      => [
                'amount'   => $product->price_amount,
                'currency' => $product->price_currency,
            ],
            'images' => $product->relationLoaded('gallery')
                ? $product->gallery->pluck('url')->values()->all()
                : [],
        ];
    }
}
