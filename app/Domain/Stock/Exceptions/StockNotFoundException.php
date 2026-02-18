<?php

declare(strict_types=1);

namespace App\Domain\Stock\Exceptions;

final class StockNotFoundException extends \DomainException
{
    public static function forProduct(string $productId): self
    {
        return new self("Stock not found for product: {$productId}");
    }
}
