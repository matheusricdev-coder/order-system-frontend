<?php

declare(strict_types=1);

namespace App\Domain\User;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ADMIN    = 'admin';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
