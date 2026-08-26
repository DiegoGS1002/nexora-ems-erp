<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use App\Models\SalesOrder;
use App\Observers\SalesOrderObserver;
use App\Tenancy\TenantContext;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(TenantContext::class);
    }

    public function boot(): void
    {
        if (config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        SalesOrder::observe(SalesOrderObserver::class);

        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}
