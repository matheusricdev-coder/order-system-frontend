<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Order\OrderItem;
use PHPUnit\Framework\TestCase;

final class OrderItemTest extends TestCase
{
    public function test_total_price_is_unit_price_times_quantity(): void
    {
        $item = new OrderItem(
            id: 'i1',
            productId: 'p1',
            quantity: 3,
            unitPrice: new Money(1000, 'BRL')
        );

        $total = $item->totalPrice();

        self::assertSame(3000, $total->amount());
        self::assertSame('BRL', $total->currency());
    }
}
