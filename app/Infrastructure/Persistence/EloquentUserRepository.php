<?php

namespace App\Infrastructure\Persistence;

use App\Application\Repositories\User\UserRepository;
use App\Domain\User\User;
use App\Models\UserModel as UserModel;
use DomainException;

final class EloquentUserRepository implements UserRepository
{
    public function findById(string $id): User
    {
        $model = UserModel::query()->find($id);

        if ($model === null) {
            throw new DomainException('User not found');
        }

        $user = new User(id: (string) $model->id, active: (bool) $model->active);

        if ($model->company_id !== null) {
            $user->assignCompany((string) $model->company_id);
        }

        return $user;
    }
}
