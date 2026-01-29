<?php

namespace App\Domain\Product;

use App\Domain\Common\Money;

final class Product
{
    private string $id;
    private string $name;
    private Money $price;
    private string $categoryId;
    private string $companyId;
    public function __construct(string $id, string $name, Money $price, string $categoryId, string $companyId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->categoryId = $categoryId;
        $this->companyId = $companyId;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): Money
    {
        return $this->price;
    }
    public function categoryId(): string
    {
        return $this->categoryId;
    }
    public function companyId(): string
    {
        return $this->companyId;
    }
}
