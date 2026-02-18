<?php

declare(strict_types=1);

namespace App\Domain\Stock\Exceptions;

final class InsufficientStockException extends \DomainException
{
    public static function forProduct(string $productId, int $requested, int $available): self
    {
        return new self("Insufficient stock");
    }
}
