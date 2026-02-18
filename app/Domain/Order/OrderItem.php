<?php

namespace App\Domain\Order;

use App\Domain\Common\Money;

final class OrderItem
{
    private string $id;
    private string $productId;
    private int $quantity;
    private Money $unitPrice;

    public function __construct(string $id, string $productId, int $quantity, Money $unitPrice)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function id(): string
    {
        return $this->id;
    }
    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function totalPrice(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
