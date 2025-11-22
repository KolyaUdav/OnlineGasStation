<?php

use App\Enums\Roles\Permissions;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/logout', 'logout');
    });

    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::middleware('permission.has:' . Permissions::WatchAllOrders->value)->group(function () {
            Route::get('/', 'filter');
        });

        Route::middleware('permission.has:' . Permissions::WatchLastOrder->value)->group(function () {
            Route::get('/last', 'getLastOrder');
        });

        Route::middleware('permission.has:' . Permissions::CreateOrder->value)->group(function () {
            Route::post('/', 'create');
        });
    });

    Route::controller(PromotionController::class)->prefix('promotions')->group(function () {
        Route::middleware('permission.has:' . Permissions::WatchPromotions->value)->group(function () {
            Route::get('/', 'filter');
            Route::get('{id}', 'getById');
        });

        Route::middleware('permission.has:' . Permissions::CreatePromotion->value)->group(function () {
            Route::post('/', 'create');
        });
    });
});
