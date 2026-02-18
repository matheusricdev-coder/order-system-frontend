<?php

namespace App\Providers;

use App\Common\IdGenerator;
use App\Common\UuidGenerator;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Product\ProductRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Application\Repositories\User\UserRepository;
use App\Infrastructure\Persistence\EloquentOrderRepository;
use App\Infrastructure\Persistence\EloquentProductRepository;
use App\Infrastructure\Persistence\EloquentStockRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IdGenerator::class, UuidGenerator::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(StockRepository::class, EloquentStockRepository::class);
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
