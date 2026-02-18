<?php

declare(strict_types=1);

namespace App\Domain\Product\Exceptions;

final class ProductNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("Product not found: {$id}");
    }
}
