<?php

declare(strict_types=1);

namespace App\Domain\Order;

enum OrderStatus: string
{
    case CREATED   = 'created';
    case PAID      = 'paid';
    case CANCELLED = 'cancelled';

    /** Guard valid state-machine transitions. */
    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::CREATED   => in_array($next, [self::PAID, self::CANCELLED], true),
            self::PAID      => false,
            self::CANCELLED => false,
        };
    }
}
