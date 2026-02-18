<?php

namespace App\Application\Repositories\Product;

use App\Domain\Product\Product;

interface ProductRepository
{
    public function findById(string $id): Product;
}
