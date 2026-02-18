<?php

namespace App\Application\Repositories\Stock;

use App\Domain\Stock\Stock;

interface StockRepository
{
    public function findByProductId(string $productId): Stock;

    public function findByProductIdForUpdate(string $productId): Stock;

    public function save(Stock $stock): void;
}
