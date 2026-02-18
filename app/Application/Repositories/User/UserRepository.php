<?php

namespace App\Application\Repositories\User;

use App\Domain\User\User;

interface UserRepository
{
    public function findById(string $id): User;
}
