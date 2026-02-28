<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserRole;
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

    public function test_user_defaults_to_customer_role(): void
    {
        $user = new User(id: 'u1');

        self::assertSame(UserRole::CUSTOMER, $user->role());
        self::assertFalse($user->isAdmin());
    }

    public function test_admin_role_is_recognized(): void
    {
        $user = new User(id: 'u1', role: UserRole::ADMIN);

        self::assertTrue($user->isAdmin());
        self::assertSame(UserRole::ADMIN, $user->role());
    }

    public function test_customer_role_is_not_admin(): void
    {
        $user = new User(id: 'u1', role: UserRole::CUSTOMER);

        self::assertFalse($user->isAdmin());
    }
}
