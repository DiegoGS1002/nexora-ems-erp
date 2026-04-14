<?php

namespace App\Services\Dashboard;

use App\Enums\SalesOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardMetricsService
{
    /** Cache local de verificações de schema (evita N+1 em loops) */
    private array $tableCache  = [];
    private array $columnCache = [];

    private function hasTable(string $table): bool
    {
        return $this->tableCache[$table] ??= Schema::hasTable($table);
    }

    private function hasColumn(string $table, string $column): bool
    {
        $key = "{$table}.{$column}";
        return $this->columnCache[$key] ??= Schema::hasColumn($table, $column);
    }

    // ──────────────────────────────────────────────────────────────
    //  KPI GERAL
    // ──────────────────────────────────────────────────────────────

    public function getOverviewKpis(): array
    {
        return [
            'faturamento' => $this->getMonthlyRevenue(now()),
            'produtos'    => $this->countActiveProducts(),
            'pedidos'     => $this->countMonthOrders(now()),
            'despesas'    => $this->getTotalPendingExpenses(),
        ];
    }

    // ──────────────────────────────────────────────────────────────
    //  DADOS PARA GRAFICOS E TABELA (6 meses)
    // ──────────────────────────────────────────────────────────────

    public function getKpiReportData(): array
    {
        $now    = now();
        $months = collect(range(5, 0))
            ->map(fn(int $offset) => $now->copy()->subMonths($offset));

        $categorias = $months
            ->map(fn(Carbon $m) => $m->translatedFormat('M/y'))
            ->values()
            ->all();

        $faturamento = $months
            ->map(fn(Carbon $m) => round($this->getMonthlyRevenue($m), 2))
            ->values()
            ->all();

        $distribuicaoData = $this->getCategoryDistribution();

        $tableRows = $months
            ->values()
            ->map(function (Carbon $month, int $index) use ($faturamento) {
                return [
                    'month_index' => $index,
                    'mes'         => $month->translatedFormat('M/y'),
                    'faturamento' => $faturamento[$index],
                    'pedidos'     => $this->countMonthOrders($month),
                ];
            })
            ->all();

        return [
            'faturamento'         => $faturamento,
            'categorias'          => $categorias,
            'distribuicao'        => $distribuicaoData['series'],
            'distribuicao_labels' => $distribuicaoData['labels'],
            'table_rows'          => $tableRows,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    //  FATURAMENTO MENSAL
    //  Prioridade: sales_orders → accounts_receivable → estoque
    // ──────────────────────────────────────────────────────────────

    private function getMonthlyRevenue(Carbon $month): float
    {
        // 1. sales_orders: pedidos faturados/aprovados no mês
        if ($this->hasTable('sales_orders')) {
            $statuses = [
                SalesOrderStatus::Approved->value,
                SalesOrderStatus::EmSeparacao->value,
                SalesOrderStatus::Invoiced->value,
                SalesOrderStatus::Delivered->value,
            ];

            $revenue = DB::table('sales_orders')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereIn('status', $statuses)
                ->sum('total_amount');

            if ($revenue > 0) {
                return round((float) $revenue, 2);
            }
        }

        // 2. accounts_receivable: valores recebidos no mês
        if ($this->hasTable('accounts_receivable') && $this->hasColumn('accounts_receivable', 'amount')) {
            $dateCol = $this->hasColumn('accounts_receivable', 'received_at') ? 'received_at' : 'created_at';

            $received = DB::table('accounts_receivable')
                ->whereYear($dateCol, $month->year)
                ->whereMonth($dateCol, $month->month)
                ->whereIn('status', ['received', 'partial'])
                ->sum('amount');

            if ($received > 0) {
                return round((float) $received, 2);
            }
        }

        // 3. Fallback: estimativa baseada em produtos criados no mês × estoque
        if ($this->hasTable('products')) {
            $val = DB::table('products')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereNull('deleted_at')
                ->selectRaw('COALESCE(SUM(COALESCE(sale_price,0) * GREATEST(COALESCE(stock,0),1)),0) as total')
                ->value('total');

            return round((float) $val, 2);
        }

        return 0.0;
    }

    // ──────────────────────────────────────────────────────────────
    //  CONTAGEM DE PRODUTOS ATIVOS
    // ──────────────────────────────────────────────────────────────

    private function countActiveProducts(): int
    {
        if (! $this->hasTable('products')) {
            return 0;
        }

        $query = DB::table('products')->whereNull('deleted_at');

        if ($this->hasColumn('products', 'is_active')) {
            $query->where('is_active', true);
        }

        return (int) $query->count();
    }

    // ──────────────────────────────────────────────────────────────
    //  CONTAGEM DE PEDIDOS DO MÊS
    //  Prioridade: sales_orders → requests
    // ──────────────────────────────────────────────────────────────

    private function countMonthOrders(Carbon $month): int
    {
        if ($this->hasTable('sales_orders')) {
            return (int) DB::table('sales_orders')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        if ($this->hasTable('requests')) {
            return (int) DB::table('requests')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return 0;
    }

    // ──────────────────────────────────────────────────────────────
    //  TOTAL DE DESPESAS (contas a pagar pendentes/vencidas)
    // ──────────────────────────────────────────────────────────────

    private function getTotalPendingExpenses(): float
    {
        $total = 0.0;

        if ($this->hasTable('accounts_payable')) {
            $amtCol = $this->hasColumn('accounts_payable', 'amount') ? 'amount' : 'value';

            if ($this->hasColumn('accounts_payable', 'status')) {
                $total += (float) DB::table('accounts_payable')
                    ->whereIn('status', ['pending', 'overdue'])
                    ->sum($amtCol);
            } else {
                $total += (float) DB::table('accounts_payable')->sum($amtCol);
            }
        }

        return round($total, 2);
    }

    // ──────────────────────────────────────────────────────────────
    //  DISTRIBUIÇÃO POR CATEGORIA
    //  Prioridade: product_categories JOIN → products.category
    // ──────────────────────────────────────────────────────────────

    private function getCategoryDistribution(): array
    {
        if (! $this->hasTable('products')) {
            return ['labels' => ['Sem dados'], 'series' => [0]];
        }

        // Usa tabela de categorias se disponível
        if ($this->hasTable('product_categories') && $this->hasColumn('products', 'product_category_id')) {
            $rows = DB::table('products')
                ->selectRaw("COALESCE(pc.name, products.category, 'Sem categoria') as category")
                ->selectRaw('COUNT(*) as total')
                ->leftJoin('product_categories as pc', 'products.product_category_id', '=', 'pc.id')
                ->whereNull('products.deleted_at')
                ->where('products.is_active', true)
                ->groupByRaw("COALESCE(pc.name, products.category, 'Sem categoria')")
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        } else {
            $query = DB::table('products')->whereNull('deleted_at');

            if ($this->hasColumn('products', 'is_active')) {
                $query->where('is_active', true);
            }

            $rows = $query
                ->selectRaw("COALESCE(NULLIF(category, ''), 'Sem categoria') as category")
                ->selectRaw('COUNT(*) as total')
                ->groupByRaw("COALESCE(NULLIF(category, ''), 'Sem categoria')")
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        if ($rows->isEmpty()) {
            return ['labels' => ['Sem produtos'], 'series' => [0]];
        }

        return [
            'labels' => $rows->pluck('category')->map(fn($v) => ucfirst((string) $v))->all(),
            'series' => $rows->pluck('total')->map(fn($v) => (int) $v)->all(),
        ];
    }

    // ──────────────────────────────────────────────────────────────
    //  PEDIDOS RECENTES
    //  Prioridade: sales_orders com cliente → vazio (sem mock)
    // ──────────────────────────────────────────────────────────────

    public function getRecentOrders(int $limit = 5): array
    {
        if (! $this->hasTable('sales_orders')) {
            return [];
        }

        $statusMap = [
            SalesOrderStatus::Draft->value        => 'Rascunho',
            SalesOrderStatus::Aberto->value       => 'Aberto',
            SalesOrderStatus::Approved->value     => 'Aprovado',
            SalesOrderStatus::EmSeparacao->value  => 'Separação',
            SalesOrderStatus::Invoiced->value     => 'Faturado',
            SalesOrderStatus::Delivered->value    => 'Entregue',
            SalesOrderStatus::Cancelled->value    => 'Cancelado',
        ];

        $query = DB::table('sales_orders as so')
            ->select([
                'so.id',
                'so.order_number',
                'so.total_amount',
                'so.status',
                'so.created_at',
            ]);

        if ($this->hasTable('clients') && $this->hasColumn('sales_orders', 'client_id')) {
            $query->addSelect('c.name as client_name')
                  ->leftJoin('clients as c', 'so.client_id', '=', 'c.id');
        } else {
            $query->selectRaw("'—' as client_name");
        }

        $orders = $query->orderByDesc('so.created_at')->limit($limit)->get();

        return $orders->map(function ($row) use ($statusMap) {
            return [
                'id'      => $row->order_number ?? ('#' . str_pad($row->id, 4, '0', STR_PAD_LEFT)),
                'cliente' => $row->client_name ?? '—',
                'valor'   => (float) ($row->total_amount ?? 0),
                'status'  => $statusMap[$row->status] ?? ucfirst((string) ($row->status ?? '')),
            ];
        })->toArray();
    }

    // ──────────────────────────────────────────────────────────────
    //  MOVIMENTAÇÕES RECENTES
    //  Prioridade: accounts_receivable + accounts_payable → stock_movements
    // ──────────────────────────────────────────────────────────────

    public function getRecentMovements(int $limit = 5): array
    {
        $movements = collect();

        // Entradas: contas a receber pagas/parciais
        if ($this->hasTable('accounts_receivable') && $this->hasColumn('accounts_receivable', 'amount')) {
            $dateCol = $this->hasColumn('accounts_receivable', 'received_at') ? 'received_at' : 'updated_at';

            $entradas = DB::table('accounts_receivable')
                ->selectRaw("description_title as descricao, amount as valor, `{$dateCol}` as data, 'entrada' as tipo")
                ->whereIn('status', ['received', 'partial'])
                ->orderByDesc($dateCol)
                ->limit($limit)
                ->get();

            $movements = $movements->merge($entradas);
        }

        // Saídas: contas a pagar pagas
        if ($this->hasTable('accounts_payable') && $this->hasColumn('accounts_payable', 'amount')) {
            $dateCol = $this->hasColumn('accounts_payable', 'payment_date') ? 'payment_date' : 'updated_at';

            $saidas = DB::table('accounts_payable')
                ->selectRaw("description_title as descricao, amount as valor, `{$dateCol}` as data, 'saida' as tipo")
                ->where('status', 'paid')
                ->orderByDesc($dateCol)
                ->limit($limit)
                ->get();

            $movements = $movements->merge($saidas);
        }

        // Retorna dados financeiros se existirem
        if ($movements->isNotEmpty()) {
            return $movements
                ->filter(fn($m) => ! empty($m->data))
                ->sortByDesc('data')
                ->take($limit)
                ->map(function ($mov) {
                    return [
                        'descricao' => $mov->descricao ?? '—',
                        'tipo'      => $mov->tipo,
                        'valor'     => (float) ($mov->valor ?? 0),
                        'data'      => Carbon::parse($mov->data)->diffForHumans(),
                    ];
                })
                ->values()
                ->toArray();
        }

        // Fallback: movimentações de estoque
        if ($this->hasTable('stock_movements')) {
            $query = DB::table('stock_movements as sm')
                ->select([
                    'sm.id',
                    'sm.type',
                    'sm.quantity',
                    'sm.origin',
                    'sm.unit_cost',
                    'sm.created_at',
                    'p.name as product_name',
                    'p.sale_price',
                ])
                ->join('products as p', 'sm.product_id', '=', 'p.id');

            if ($this->hasColumn('stock_movements', 'user_id') && $this->hasTable('users')) {
                $query->leftJoin('users as u', 'sm.user_id', '=', 'u.id')
                      ->addSelect('u.name as user_name');
            }

            $rows = $query->orderByDesc('sm.created_at')->limit($limit)->get();

            return $rows->map(function ($mov) {
                $valor = (float) $mov->quantity * ((float) ($mov->sale_price ?? $mov->unit_cost ?? 0));

                return [
                    'descricao' => ($mov->origin ?? 'Movimento') . ' - ' . ($mov->product_name ?? ''),
                    'tipo'      => $mov->type === 'output' ? 'saida' : 'entrada',
                    'valor'     => round($valor, 2),
                    'data'      => Carbon::parse($mov->created_at)->diffForHumans(),
                ];
            })->toArray();
        }

        return [];
    }

    // ──────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────

    private function safeCount(string $table): int
    {
        if (! $this->hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }
}
