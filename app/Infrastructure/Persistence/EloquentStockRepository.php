<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Stock\Exceptions\StockNotFoundException;
use App\Domain\Stock\Stock;
use App\Models\StockModel;

final class EloquentStockRepository implements StockRepository
{
    public function findByProductId(string $productId): Stock
    {
        $model = StockModel::query()->where('product_id', $productId)->first();

        if ($model === null) {
            throw StockNotFoundException::forProduct($productId);
        }

        return $this->toDomain($model);
    }

    public function findByProductIdForUpdate(string $productId): Stock
    {
        $model = StockModel::query()
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if ($model === null) {
            throw StockNotFoundException::forProduct($productId);
        }

        return $this->toDomain($model);
    }

    public function save(Stock $stock): void
    {
        StockModel::query()->updateOrCreate(
            ['id' => $stock->id()],
            [
                'product_id'        => $stock->productId(),
                'quantity_total'    => $stock->total(),
                'quantity_reserved' => $stock->reserved(),
            ]
        );
    }

    private function toDomain(StockModel $model): Stock
    {
        $stock    = new Stock(
            id: (string) $model->id,
            productId: (string) $model->product_id,
            quantityTotal: (int) $model->quantity_total,
        );
        $reserved = (int) $model->quantity_reserved;

        if ($reserved > 0) {
            $stock->reserve($reserved);
        }

        return $stock;
    }
}
