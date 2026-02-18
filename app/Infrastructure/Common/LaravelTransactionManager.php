<?php

namespace App\Infrastructure\Common;

use App\Application\Common\TransactionManager;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements TransactionManager
{
    public function run(callable $fn): mixed
    {
        return DB::transaction($fn);
    }
}
