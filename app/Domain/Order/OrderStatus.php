<?php

namespace App\Domain\Order;

enum OrderStatus: string
{
    case CREATED = 'created';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
}
