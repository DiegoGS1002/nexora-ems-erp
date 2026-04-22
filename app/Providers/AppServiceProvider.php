<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\SalesOrder;
use App\Observers\SalesOrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS only when explicitly enabled via FORCE_HTTPS=true in .env
        // Do NOT rely on APP_ENV to decide this — always configure explicitly.
        if (config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        SalesOrder::observe(SalesOrderObserver::class);
    }
}
