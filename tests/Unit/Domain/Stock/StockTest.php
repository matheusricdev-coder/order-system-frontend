<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Stock;

use App\Domain\Stock\Stock;
use DomainException;
use PHPUnit\Framework\TestCase;

final class StockTest extends TestCase
{
    public function test_it_cannot_be_created_with_negative_total(): void
    {
        $this->expectException(DomainException::class);

        new Stock(id: 's1', productId: 'p1', quantityTotal: -1);
    }

    public function test_available_is_total_minus_reserved(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);

        self::assertSame(10, $stock->available());

        $stock->reserve(3);

        self::assertSame(7, $stock->available());
        self::assertSame(3, $stock->reserved());
        self::assertSame(10, $stock->total());
    }

    public function test_reserve_increases_reserved(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);

        $stock->reserve(4);

        self::assertSame(4, $stock->reserved());
        self::assertSame(6, $stock->available());
    }

    public function test_reserve_rejects_non_positive_quantity(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);

        $this->expectException(DomainException::class);
        $stock->reserve(0);
    }

    public function test_reserve_rejects_when_insufficient_stock(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 2);

        $this->expectException(DomainException::class);
        $stock->reserve(3);
    }

    public function test_release_decreases_reserved(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);
        $stock->reserve(5);

        $stock->release(2);

        self::assertSame(3, $stock->reserved());
        self::assertSame(7, $stock->available());
        self::assertSame(10, $stock->total());
    }

    public function test_release_rejects_when_more_than_reserved(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);
        $stock->reserve(1);

        $this->expectException(DomainException::class);
        $stock->release(2);
    }

    public function test_consume_decreases_reserved_and_total(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);
        $stock->reserve(4);

        $stock->consume(3);

        self::assertSame(1, $stock->reserved());
        self::assertSame(7, $stock->total());
        self::assertSame(6, $stock->available());
    }

    public function test_consume_rejects_when_more_than_reserved(): void
    {
        $stock = new Stock(id: 's1', productId: 'p1', quantityTotal: 10);
        $stock->reserve(2);

        $this->expectException(DomainException::class);
        $stock->consume(3);
    }
}
