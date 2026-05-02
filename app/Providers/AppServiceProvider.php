<?php

namespace App\Providers;

use App\Contracts\IOrderCheckHandler;
use App\Services\API\OrderCheckHandlerNode;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            IOrderCheckHandler::class,
            OrderCheckHandlerNode::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
