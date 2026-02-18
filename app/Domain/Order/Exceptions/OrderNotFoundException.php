<?php

declare(strict_types=1);

namespace App\Domain\Order\Exceptions;

final class OrderNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self('Order not found');
    }
}
