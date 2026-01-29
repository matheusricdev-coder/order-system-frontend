<?php

namespace App\Domain\User;

use DomainException;

final class User
{
    private string $id;
    private bool $active;
    private ?string $companyId = null;

    public function __construct(string $id, bool $active = true)
    {
        $this->id = $id;
        $this->active = $active;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function hasCompany(): bool
    {
        return $this->companyId !== null;
    }

    public function companyId(): ?string
    {
        return $this->companyId;
    }

    public function assignCompany(string $companyId): void
    {
        if ($this->companyId !== null) {
            throw new \DomainException('User already has a company');
        }

        $this->companyId = $companyId;
    }
}
