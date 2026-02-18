<?php

namespace App\Application\Common;

interface TransactionManager
{
    /**
     * @template T
     * @param callable():T $fn
     * @return T
     */
    public function run(callable $fn): mixed;
}
