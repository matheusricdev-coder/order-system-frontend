<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User;

use App\Domain\User\User;
use DomainException;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function test_user_starts_active_by_default(): void
    {
        $user = new User(id: 'u1');

        self::assertTrue($user->isActive());
        self::assertFalse($user->hasCompany());
        self::assertNull($user->companyId());
    }

    public function test_assign_company_sets_company_id_once(): void
    {
        $user = new User(id: 'u1');

        $user->assignCompany('c1');

        self::assertTrue($user->hasCompany());
        self::assertSame('c1', $user->companyId());
    }

    public function test_assign_company_cannot_be_called_twice(): void
    {
        $user = new User(id: 'u1');
        $user->assignCompany('c1');

        $this->expectException(DomainException::class);

        $user->assignCompany('c2');
    }
}
