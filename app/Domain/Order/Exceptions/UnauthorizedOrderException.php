<?php

declare(strict_types=1);

namespace App\Domain\Order\Exceptions;

final class UnauthorizedOrderException extends \DomainException
{
    public static function notOwner(string $orderId): self
    {
        return new self("You are not authorized to modify order: {$orderId}");
    }
}
