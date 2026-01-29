<?php

namespace App\Domain\Stock;

use DomainException;

final class Stock
{
    private string $id;
    private string $productId;
    private int $quantityTotal;
    private int $quantityReserved = 0;

    public function __construct(string $id, string $productId, int $quantityTotal)
    {
        if ($quantityTotal < 0) {
            throw new DomainException('Total quantity cannot be negative');
        }

        $this->id = $id;
        $this->productId = $productId;
        $this->quantityTotal = $quantityTotal;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function total(): int
    {
        return $this->quantityTotal;
    }

    public function reserved(): int
    {
        return $this->quantityReserved;
    }

    public function available(): int
    {
        return $this->quantityTotal - $this->quantityReserved;
    }

    public function reserve(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new DomainException('Reserve quantity must be positive');
        }

        if ($quantity > $this->available()) {
            throw new DomainException('Insufficient stock');
        }

        $this->quantityReserved += $quantity;
    }

    public function release(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new DomainException('Release quantity must be positive');
        }

        if ($quantity > $this->quantityReserved) {
            throw new DomainException('Cannot release more than reserved');
        }

        $this->quantityReserved -= $quantity;
    }

    public function consume(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new DomainException('Consume quantity must be positive');
        }

        if ($quantity > $this->quantityReserved) {
            throw new DomainException('Cannot consume more than reserved');
        }

        $this->quantityReserved -= $quantity;
        $this->quantityTotal -= $quantity;
    }
}
