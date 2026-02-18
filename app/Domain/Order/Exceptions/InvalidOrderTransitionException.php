<?php

declare(strict_types=1);

namespace App\Domain\Order\Exceptions;

final class InvalidOrderTransitionException extends \DomainException
{
    public static function cannotBePaid(string $currentStatus): self
    {
        return new self("Order cannot be paid");
    }

    public static function cannotBeCancelled(string $currentStatus): self
    {
        return new self("Order cannot be cancelled");
    }
}
