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
            'despesas' => $this->getTotalExpenses(),
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

    /**
     * Calcula faturamento estimado baseado no valor do estoque
     */
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

    /**
     * Calcula faturamento mensal estimado baseado em produtos criados no mês
     */
    private function monthlyEstimatedRevenue(Carbon $month): float
    {
        if (! Schema::hasTable('products')) {
            return 0.0;
        }

        // Tenta calcular com base em vendas/movimentações se existir
        $movementRevenue = $this->calculateRevenueFromMovements($month);
        if ($movementRevenue > 0) {
            return $movementRevenue;
        }

        // Fallback: estima com base no valor dos produtos criados no mês
        return (float) DB::table('products')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->selectRaw('COALESCE(SUM(COALESCE(sale_price, 0) * GREATEST(COALESCE(stock, 0), 1)), 0) as total')
            ->value('total');
    }

    /**
     * Calcula receita baseada em movimentações de estoque (saídas)
     */
    private function calculateRevenueFromMovements(Carbon $month): float
    {
        if (! Schema::hasTable('stock_movements')) {
            return 0.0;
        }

        // Soma o valor das saídas de estoque multiplicado pelo preço de venda
        $revenue = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->whereYear('stock_movements.created_at', $month->year)
            ->whereMonth('stock_movements.created_at', $month->month)
            ->where('stock_movements.type', 'output')
            ->selectRaw('COALESCE(SUM(stock_movements.quantity * COALESCE(products.sale_price, stock_movements.unit_cost, 0)), 0) as total')
            ->value('total');

        return round((float) $revenue, 2);
    }

    /**
     * Calcula total de despesas
     */
    private function getTotalExpenses(): float
    {
        $total = 0.0;

        // Despesas de contas a pagar
        if (Schema::hasTable('accounts_payable')) {
            $column = Schema::hasColumn('accounts_payable', 'amount') ? 'amount' : 'value';
            $total += (float) DB::table('accounts_payable')->sum($column);
        }

        // Custos de produtos (compras/entradas)
        if (Schema::hasTable('stock_movements')) {
            $purchases = DB::table('stock_movements')
                ->where('type', 'input')
                ->whereNotNull('unit_cost')
                ->selectRaw('COALESCE(SUM(quantity * unit_cost), 0) as total')
                ->value('total');

            $total += (float) $purchases;
        }

        return round($total, 2);
    }

    /**
     * Distribuição por categoria de produtos
     */
    private function categoryDistribution(): array
    {
        if (! Schema::hasTable('products')) {
            return [
                'labels' => ['Sem dados'],
                'series' => [0],
            ];
        }

        $rows = DB::table('products')
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Sem categoria') as category")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($rows->isEmpty()) {
            return [
                'labels' => ['Sem produtos'],
                'series' => [0],
            ];
        }

        return [
            'labels' => $rows->pluck('category')->map(fn ($item) => ucfirst((string) $item))->all(),
            'series' => $rows->pluck('total')->map(fn ($item) => (float) $item)->all(),
        ];
    }

    /**
     * Conta pedidos no mês
     */
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

    /**
     * Conta registros de forma segura
     */
    private function safeCount(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    /**
     * Soma coluna de forma segura
     */
    private function safeSum(string $table, string $column): float
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0.0;
        }

        return (float) DB::table($table)->sum($column);
    }

    /**
     * Retorna movimentações recentes para o dashboard
     */
    public function getRecentMovements(int $limit = 5): array
    {
        if (! Schema::hasTable('stock_movements')) {
            return [];
        }

        $movements = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->join('users', 'stock_movements.user_id', '=', 'users.id')
            ->select([
                'stock_movements.id',
                'stock_movements.type',
                'stock_movements.quantity',
                'stock_movements.origin',
                'stock_movements.created_at',
                'products.name as product_name',
                'products.sale_price',
                'users.name as user_name',
            ])
            ->orderBy('stock_movements.created_at', 'desc')
            ->limit($limit)
            ->get();

        return $movements->map(function ($mov) {
            $valor = $mov->quantity * ($mov->sale_price ?? 0);
            $tipo = $mov->type === 'output' ? 'saida' : 'entrada';

            return [
                'descricao' => "{$mov->origin} - {$mov->product_name}",
                'tipo' => $tipo,
                'valor' => $valor,
                'data' => Carbon::parse($mov->created_at)->diffForHumans(),
            ];
        })->toArray();
    }
}
