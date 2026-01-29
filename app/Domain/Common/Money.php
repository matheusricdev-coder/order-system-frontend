<?php

namespace App\Domain\Common;

final class Money
{
    private int $amount;
    private string $currency;

    public function __construct(int $amount, string $currency)
    {
        if ($amount < 0) {
            throw new \DomainException('Amount cannot be negative');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function multiply(int $factor): Money
    {
        if ($factor <= 0) {
            throw new \DomainException('Factor cannot be negative');
        }

        return new Money($this->amount * $factor, $this->currency);
    }
}
