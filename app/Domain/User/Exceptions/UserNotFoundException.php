<?php

declare(strict_types=1);

namespace App\Domain\User\Exceptions;

final class UserNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("User not found");
    }
}
