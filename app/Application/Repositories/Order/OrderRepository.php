<?php

namespace App\Application\Repositories\Order;

use App\Domain\Order\Order;

interface OrderRepository
{
    public function save(Order $order): void;

    public function findById(string $id): Order;

    public function findByIdForUpdate(string $id): Order;
}
