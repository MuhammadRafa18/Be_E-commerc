<?php

namespace App\Providers;

use App\Handlers\Order\OrderHandler;
use App\Handlers\Order\OrderHandlerInterface;
use Midtrans\Config as MidtransConfig;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            OrderHandlerInterface::class,
            OrderHandler::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MidtransConfig::$serverKey = config('Midtrans.server_key');
        MidtransConfig::$clientKey = config('Midtrans.client_key');
        MidtransConfig::$isProduction = config('Midtrans.is_production');
        MidtransConfig::$isSanitized = config('Midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('Midtrans.is_3ds');
    }
}
