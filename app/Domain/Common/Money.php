<?php

declare(strict_types=1);

namespace App\Domain\Common;

final class Money
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Amount cannot be negative');
        }
    }

    public function amount(): int { return $this->amount; }
    public function currency(): string { return $this->currency; }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function isGreaterThan(self $other): bool
    {
        $this->ensureSameCurrency($other);
        return $this->amount > $other->amount;
    }

    /** Format as human-readable string (e.g. 1.500,00 BRL). Amount is stored in cents. */
    public function format(): string
    {
        return number_format($this->amount / 100, 2, ',', '.') . ' ' . $this->currency;
    }

    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException(
                sprintf('Currency mismatch: cannot operate on %s and %s', $this->currency, $other->currency)
            );
        }
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function multiply(int $factor): self
    {
        if ($factor <= 0) {
            throw new \DomainException('Multiply factor must be a positive integer');
        }

        return new self($this->amount * $factor, $this->currency);
    }
}
