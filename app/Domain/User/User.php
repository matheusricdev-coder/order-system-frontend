<?php

declare(strict_types=1);

namespace App\Domain\User;

final class User
{
    private string $id;
    private bool $active;
    private UserRole $role;
    private ?string $companyId = null;

    public function __construct(string $id, bool $active = true, UserRole $role = UserRole::CUSTOMER)
    {
        $this->id     = $id;
        $this->active = $active;
        $this->role   = $role;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function role(): UserRole
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
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
