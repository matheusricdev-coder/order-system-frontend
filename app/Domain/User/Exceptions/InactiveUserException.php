<?php

declare(strict_types=1);

namespace App\Domain\User\Exceptions;

final class InactiveUserException extends \DomainException
{
    public static function forUser(string $userId): self
    {
        return new self("Inactive user cannot create orders");
    }
}
