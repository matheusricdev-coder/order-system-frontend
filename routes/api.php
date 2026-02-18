<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'create']);
Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
