<?php

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\Product\ProductRepository;
use App\Domain\Common\Money;
use App\Domain\Product\Product;
use App\Models\ProductModel;
use DomainException;

final class EloquentProductRepository implements ProductRepository
{
    public function findById(string $id): Product
    {
        $model = ProductModel::query()->find($id);

        if ($model === null) {
            throw new DomainException('Product not found');
        }

        return new Product(
            id: (string) $model->id,
            name: (string) $model->name,
            price: new Money((int) $model->price_amount, (string) $model->price_currency),
            categoryId: (string) $model->category_id,
            companyId: (string) $model->company_id
        );
    }
}
