<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Common;

use App\Domain\Common\Money;
use DomainException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_it_cannot_be_created_with_negative_amount(): void
    {
        $this->expectException(DomainException::class);

        new Money(-1, 'BRL');
    }

    public function test_equals_compares_amount_and_currency(): void
    {
        $a = new Money(1000, 'BRL');
        $b = new Money(1000, 'BRL');
        $c = new Money(1000, 'USD');
        $d = new Money(2000, 'BRL');

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
        self::assertFalse($a->equals($d));
    }

    public function test_multiply_returns_new_money_with_scaled_amount(): void
    {
        $money = new Money(500, 'BRL');

        $result = $money->multiply(3);

        self::assertSame(1500, $result->amount());
        self::assertSame('BRL', $result->currency());
    }

    public function test_multiply_rejects_zero_or_negative_factor(): void
    {
        $money = new Money(500, 'BRL');

        $this->expectException(DomainException::class);
        $money->multiply(0);
    }

    public function test_add_returns_sum_of_two_moneys(): void
    {
        $result = (new Money(1000, 'BRL'))->add(new Money(500, 'BRL'));

        self::assertSame(1500, $result->amount());
        self::assertSame('BRL', $result->currency());
    }

    public function test_add_throws_on_currency_mismatch(): void
    {
        $this->expectException(DomainException::class);

        (new Money(1000, 'BRL'))->add(new Money(500, 'USD'));
    }

    public function test_is_greater_than_compares_amounts(): void
    {
        self::assertTrue((new Money(1500, 'BRL'))->isGreaterThan(new Money(1000, 'BRL')));
        self::assertFalse((new Money(1000, 'BRL'))->isGreaterThan(new Money(1500, 'BRL')));
        self::assertFalse((new Money(1000, 'BRL'))->isGreaterThan(new Money(1000, 'BRL')));
    }

    public function test_is_greater_than_throws_on_currency_mismatch(): void
    {
        $this->expectException(DomainException::class);

        (new Money(1000, 'BRL'))->isGreaterThan(new Money(500, 'USD'));
    }

    public function test_format_returns_human_readable_string(): void
    {
        self::assertSame('15,00 BRL', (new Money(1500, 'BRL'))->format());
        self::assertSame('1.500,50 BRL', (new Money(150050, 'BRL'))->format());
    }
}
