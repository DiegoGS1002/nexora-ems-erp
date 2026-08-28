<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnforceMidnightSession;
use App\Http\Middleware\MaintenanceERP;
use App\Http\Middleware\ResolveTenantContext;
use App\Jobs\SynchronizeNFeDistribution;
use App\Models\Company;
use App\Jobs\ReconcileFiscalNoteStatus;
use App\Models\FiscalNote;
use App\Services\FiscalArchiveService;

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
            'tenant'           => ResolveTenantContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Fechar tickets inativos a cada 5 minutos
        $schedule->command('tickets:fechar-inativos')->everyFiveMinutes();
        $schedule->call(function (): void {
            Company::where('is_active', true)->each(fn (Company $company) =>
                SynchronizeNFeDistribution::dispatch($company->id, config('nfe.environment') === 1 ? 'production' : 'homologation')
            );
        })->everyFifteenMinutes()->withoutOverlapping();
        $schedule->call(function (): void {
            FiscalNote::whereNotNull('access_key')
                ->whereIn('status', ['sent', 'authorized'])
                ->each(fn (FiscalNote $note) => ReconcileFiscalNoteStatus::dispatch($note->id));
        })->hourly()->withoutOverlapping();
        $schedule->call(function (): void {
            Company::where('is_active', true)->each(fn (Company $company) =>
                app(FiscalArchiveService::class)->backup($company)
            );
        })->dailyAt('02:30')->withoutOverlapping();
    })
    ->create();
