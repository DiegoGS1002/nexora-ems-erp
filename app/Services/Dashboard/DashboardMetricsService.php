<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardMetricsService
{
    public function getOverviewKpis(): array
    {
        $faturamento = $this->estimatedRevenue();

        return [
            'faturamento' => $faturamento,
            'produtos' => $this->safeCount('products'),
            'pedidos' => $this->safeCount('requests'),
            'despesas' => $this->safeSum('accounts_payable', 'value'),
        ];
    }

    public function getKpiReportData(): array
    {
        $now = now();
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => $now->copy()->subMonths($offset));

        $categorias = $months
            ->map(fn (Carbon $month) => $month->translatedFormat('M/y'))
            ->values()
            ->all();

        $faturamento = $months
            ->map(fn (Carbon $month) => round($this->monthlyEstimatedRevenue($month), 2))
            ->values()
            ->all();

        $distribuicaoData = $this->categoryDistribution();

        $tableRows = $months
            ->values()
            ->map(function (Carbon $month, int $index) use ($faturamento) {
                return [
                    'month_index' => $index,
                    'mes' => $month->translatedFormat('M/y'),
                    'faturamento' => $faturamento[$index],
                    'pedidos' => $this->ordersForMonth($month),
                ];
            })
            ->all();

        return [
            'faturamento' => $faturamento,
            'categorias' => $categorias,
            'distribuicao' => $distribuicaoData['series'],
            'distribuicao_labels' => $distribuicaoData['labels'],
            'table_rows' => $tableRows,
        ];
    }

    private function estimatedRevenue(): float
    {
        if (! Schema::hasTable('products')) {
            return 0.0;
        }

        $value = (float) DB::table('products')
            ->selectRaw('COALESCE(SUM(COALESCE(sale_price, 0) * COALESCE(stock, 0)), 0) as total')
            ->value('total');

        return round($value, 2);
    }

    private function monthlyEstimatedRevenue(Carbon $month): float
    {
        if (! Schema::hasTable('products')) {
            return 0.0;
        }

        return (float) DB::table('products')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->selectRaw('COALESCE(SUM(COALESCE(sale_price, 0) * COALESCE(stock, 0)), 0) as total')
            ->value('total');
    }

    private function categoryDistribution(): array
    {
        if (! Schema::hasTable('products')) {
            return [
                'labels' => [],
                'series' => [],
            ];
        }

        $rows = DB::table('products')
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Sem categoria') as category")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        if ($rows->isEmpty()) {
            return [
                'labels' => [],
                'series' => [],
            ];
        }

        return [
            'labels' => $rows->pluck('category')->map(fn (string $item) => ucfirst($item))->all(),
            'series' => $rows->pluck('total')->map(fn (int $item) => (float) $item)->all(),
        ];
    }

    private function ordersForMonth(Carbon $targetMonth): int
    {
        if (! Schema::hasTable('requests')) {
            return 0;
        }

        return (int) DB::table('requests')
            ->whereYear('created_at', $targetMonth->year)
            ->whereMonth('created_at', $targetMonth->month)
            ->count();
    }

    private function safeCount(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    private function safeSum(string $table, string $column): float
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0.0;
        }

        return (float) DB::table($table)->sum($column);
    }
}

