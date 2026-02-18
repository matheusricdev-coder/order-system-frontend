<?php

namespace App\Providers;

use App\Common\IdGenerator;
use App\Common\UuidGenerator;
use App\Application\Common\DomainEventBus;
use App\Application\Common\TransactionManager;
use App\Application\Order\GetOrder\GetOrderReadModelRepository;
use App\Application\Order\ListOrders\ListOrdersReadModelRepository;
use App\Infrastructure\Common\LaravelDomainEventBus;
use App\Infrastructure\Common\LaravelTransactionManager;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Product\ProductRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Application\Repositories\User\UserRepository;
use App\Infrastructure\Persistence\EloquentOrderReadModelRepository;
use App\Infrastructure\Persistence\EloquentOrderRepository;
use App\Infrastructure\Persistence\EloquentProductRepository;
use App\Infrastructure\Persistence\EloquentStockRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
        $this->app->bind(IdGenerator::class, UuidGenerator::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(StockRepository::class, EloquentStockRepository::class);
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
        $this->app->bind(GetOrderReadModelRepository::class, EloquentOrderReadModelRepository::class);
        $this->app->bind(ListOrdersReadModelRepository::class, EloquentOrderReadModelRepository::class);
        $this->app->bind(DomainEventBus::class, LaravelDomainEventBus::class);
    }

    public function boot(): void
    {
        RateLimiter::for('api', static function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
