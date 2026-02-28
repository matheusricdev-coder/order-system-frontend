<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\User\UserRepository;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserRole;
use App\Models\UserModel;

final class EloquentUserRepository implements UserRepository
{
    public function findById(string $id): User
    {
        $model = UserModel::query()->find($id);

        if ($model === null) {
            throw UserNotFoundException::withId($id);
        }

        $user = new User(
            id: (string) $model->id,
            active: (bool) $model->active,
            role: UserRole::from($model->role ?? 'customer'),
        );

        if ($model->company_id !== null) {
            $user->assignCompany((string) $model->company_id);
        }

        return $user;
    }
}
