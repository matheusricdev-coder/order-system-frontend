<?php

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Stock\Stock;
use App\Models\StockModel;
use DomainException;

final class EloquentStockRepository implements StockRepository
{
    public function findByProductId(string $productId): Stock
    {
        $model = StockModel::query()->where('product_id', $productId)->first();

        if ($model === null) {
            throw new DomainException('Stock not found for product');
        }

        $stock = new Stock(
            id: (string) $model->id,
            productId: (string) $model->product_id,
            quantityTotal: (int) $model->quantity_total
        );

        $reserved = (int) $model->quantity_reserved;
        if ($reserved > 0) {
            $stock->reserve($reserved);
        }

        return $stock;
    }

    public function save(Stock $stock): void
    {
        StockModel::query()->updateOrCreate(
            ['id' => $stock->id()],
            [
                'product_id' => $stock->productId(),
                'quantity_total' => $stock->total(),
                'quantity_reserved' => $stock->reserved(),
            ]
        );
    }
}
