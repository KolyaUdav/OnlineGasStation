<?php

namespace App\Providers;

use App\Contracts\IOrderCheckHandler;
use App\Contracts\IPriceHandler;
use App\Contracts\IPromotionsHandler;
use App\Services\API\OrderCheckHandlerNode;
use App\Services\API\PriceHandlerCached;
use App\Services\API\PriceHandlerGo;
use App\Services\API\PromotionsHandlerGo;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PriceHandlerGo::class);

        $this->app->bind(IPriceHandler::class, function ($app) {
            return new PriceHandlerCached($app->make(PriceHandlerGo::class));
        });

        $this->app->bind(
            IOrderCheckHandler::class,
            OrderCheckHandlerNode::class
        );

        $this->app->bind(
            IPromotionsHandler::class,
            PromotionsHandlerGo::class
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
