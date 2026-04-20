<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnforceMidnightSession;
use App\Http\Middleware\MaintenanceERP;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'            => EnsureUserIsAdmin::class,
            'midnight.session' => EnforceMidnightSession::class,
            'maintenance.erp'  => MaintenanceERP::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Fechar tickets inativos a cada 5 minutos
        $schedule->command('tickets:fechar-inativos')->everyFiveMinutes();
    })
    ->create();
