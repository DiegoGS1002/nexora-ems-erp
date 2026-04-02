<?php

use App\Services\Dashboard\DashboardMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns only real values for dashboard metrics when there is no data', function () {
    $service = app(DashboardMetricsService::class);

    $overview = $service->getOverviewKpis();
    $report = $service->getKpiReportData();

    expect($overview['faturamento'])->toBe(0.0)
        ->and($overview['produtos'])->toBe(0)
        ->and($overview['pedidos'])->toBe(0)
        ->and($overview['despesas'])->toBe(0.0)
        ->and($report['faturamento'])->each->toBe(0.0)
        ->and($report['table_rows'])->each(fn (array $row) => expect($row['pedidos'])->toBe(0))
        ->and($report['distribuicao'])->toBe([])
        ->and($report['distribuicao_labels'])->toBe([]);
});

