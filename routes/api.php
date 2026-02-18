<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

$apiV1 = static function (): void {
    Route::get('/products', [CatalogController::class, 'index']);
    Route::get('/products/{id}', [CatalogController::class, 'show']);
    Route::get('/categories', [CatalogController::class, 'categories']);

    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::get('/companies/{id}/products', [CompanyController::class, 'products']);

    Route::get('/stocks/{productId}', [StockController::class, 'show']);
    Route::get('/products/{id}/stock', [StockController::class, 'showByProduct']);

    Route::post('/orders', [OrderController::class, 'create']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders', [OrderController::class, 'index']);

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth.mock');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth.mock');
};

Route::prefix('v1')->middleware('throttle:api')->group($apiV1);

// Backward compatibility while clients migrate to /api/v1.
Route::middleware('throttle:api')->group($apiV1);
