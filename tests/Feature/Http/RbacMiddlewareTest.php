<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class RbacMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function loginAs(string $role): string
    {
        UserModel::query()->create([
            'id'         => (string) str()->uuid(),
            'name'       => 'Test',
            'surname'    => 'User',
            'birth_date' => '1990-01-01',
            'email'      => "{$role}@example.com",
            'password'   => Hash::make('secret123'),
            'active'     => true,
            'role'       => $role,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => "{$role}@example.com",
            'password' => 'secret123',
        ])->assertOk();

        return $response->json('data.token.value');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Admin route — unauthenticated
    // ──────────────────────────────────────────────────────────────────────────

    public function test_unauthenticated_request_to_admin_route_returns_401(): void
    {
        $this->getJson('/api/v1/admin/ping')
            ->assertStatus(401);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Admin route — customer role (should be forbidden)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_customer_cannot_access_admin_routes(): void
    {
        $token = $this->loginAs('customer');

        $this->getJson('/api/v1/admin/ping', ['Authorization' => "Bearer {$token}"])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Admin route — admin role (should succeed)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_admin_can_access_admin_routes(): void
    {
        $token = $this->loginAs('admin');

        $this->getJson('/api/v1/admin/ping', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('status', 'admin-ok');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Domain — UserRole::isAdmin()
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_domain_entity_exposes_role_correctly(): void
    {
        $userRepo  = app(\App\Application\Repositories\User\UserRepository::class);

        $adminId = (string) str()->uuid();
        UserModel::query()->create([
            'id'         => $adminId,
            'name'       => 'Admin',
            'surname'    => 'User',
            'birth_date' => '1990-01-01',
            'email'      => 'admin_domain@example.com',
            'password'   => Hash::make('secret123'),
            'active'     => true,
            'role'       => 'admin',
        ]);

        $customerId = (string) str()->uuid();
        UserModel::query()->create([
            'id'         => $customerId,
            'name'       => 'Customer',
            'surname'    => 'User',
            'birth_date' => '1995-05-05',
            'email'      => 'customer_domain@example.com',
            'password'   => Hash::make('secret123'),
            'active'     => true,
            'role'       => 'customer',
        ]);

        $admin    = $userRepo->findById($adminId);
        $customer = $userRepo->findById($customerId);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($customer->isAdmin());
        $this->assertEquals(\App\Domain\User\UserRole::ADMIN, $admin->role());
        $this->assertEquals(\App\Domain\User\UserRole::CUSTOMER, $customer->role());
    }
}
