<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RegisterEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_a_new_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'João',
            'surname'               => 'Silva',
            'birth_date'            => '1995-06-15',
            'email'                 => 'joao@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'joao@example.com')
            ->assertJsonPath('data.user.name', 'João')
            ->assertJsonPath('data.token.type', 'Bearer')
            ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'surname', 'email'], 'token' => ['type', 'value']]]);
    }

    public function test_it_returns_422_for_duplicate_email(): void
    {
        $payload = [
            'name'                  => 'Ana',
            'surname'               => 'Costa',
            'birth_date'            => '1990-01-01',
            'email'                 => 'ana@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        $this->postJson('/api/v1/auth/register', $payload)->assertCreated();

        $this->postJson('/api/v1/auth/register', $payload)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_it_returns_422_for_missing_required_fields(): void
    {
        $this->postJson('/api/v1/auth/register', [])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_it_returns_422_when_passwords_do_not_match(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Maria',
            'surname'               => 'Oliveira',
            'birth_date'            => '1992-03-20',
            'email'                 => 'maria@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'different',
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_registered_user_can_immediately_login(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Carlos',
            'surname'               => 'Mendes',
            'birth_date'            => '1988-11-30',
            'email'                 => 'carlos@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertCreated();

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'carlos@example.com',
            'password' => 'secret1234',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'carlos@example.com')
            ->assertJsonStructure(['data' => ['token' => ['type', 'value']]]);
    }
}
