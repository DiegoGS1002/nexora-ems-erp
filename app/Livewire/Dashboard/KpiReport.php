<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardMetricsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - Indicadores KPI')]
class KpiReport extends Component
{
    public array $kpis = [];

    public array $faturamento = [];

    public array $categorias = [];

    public array $distribuicao = [];

    public array $distribuicaoLabels = [];

    public array $tableRows = [];

    public array $desempenhoStats = [];

    public array $comparativos = [];

    public array $barMeta = [];

    public array $barRealizado = [];

    public string $search = '';

    public ?int $selectedMonth = null;

    protected DashboardMetricsService $metricsService;

    public function boot(DashboardMetricsService $metricsService): void
    {
        $this->metricsService = $metricsService;
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function refreshData(): void
    {
        $this->loadData();
    }

    #[On('filtrarMes')]
    public function filtrarMes(int|string $mes): void
    {
        if (! is_numeric($mes)) {
            return;
        }

        $this->selectedMonth = (int) $mes;
    }

    public function clearMonthFilter(): void
    {
        $this->selectedMonth = null;
    }

    public function loadData(): void
    {
        // Carrega KPIs reais
        $kpisData = $this->metricsService->getOverviewKpis();

        // Carrega dados dos relatórios
        $reportData = $this->metricsService->getKpiReportData();

        // Calcula tendências
        $faturamentoTrend = $this->calculateTrend($reportData['faturamento']);
        $pedidosTrend = $this->calculateTrend(array_column($reportData['table_rows'], 'pedidos'));

        $this->kpis = [
            [
                'title'     => 'Faturamento',
                'value'     => $kpisData['faturamento'],
                'currency'  => true,
                'iconBg'    => '#EFF6FF',
                'iconColor' => '#3B82F6',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
                'trend'     => $faturamentoTrend,
            ],
            [
                'title'     => 'Produtos',
                'value'     => $kpisData['produtos'],
                'currency'  => false,
                'iconBg'    => '#F5F3FF',
                'iconColor' => '#7C3AED',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
                'trend'     => null,
            ],
            [
                'title'     => 'Pedidos',
                'value'     => $kpisData['pedidos'],
                'currency'  => false,
                'iconBg'    => '#FFFBEB',
                'iconColor' => '#D97706',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
                'trend'     => $pedidosTrend,
            ],
            [
                'title'     => 'Despesas',
                'value'     => $kpisData['despesas'],
                'currency'  => true,
                'iconBg'    => '#FFF1F2',
                'iconColor' => '#E11D48',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
                'trend'     => null,
            ],
        ];

        $this->faturamento = $reportData['faturamento'];
        $this->categorias = $reportData['categorias'];
        $this->distribuicao = $reportData['distribuicao'];
        $this->distribuicaoLabels = $reportData['distribuicao_labels'];
        $this->tableRows = $reportData['table_rows'];

        // Calcula estatísticas de desempenho
        $totalFaturamento = array_sum($this->faturamento);
        $totalPedidos = array_sum(array_column($this->tableRows, 'pedidos'));
        $ticketMedio = $totalPedidos > 0 ? $totalFaturamento / $totalPedidos : 0;

        $metaMensal = 150000;
        $metaPedidos = 100;
        $metaTicket = 2000;

        $this->desempenhoStats = [
            [
                'label' => 'Meta Mensal',
                'meta' => $metaMensal,
                'realizado' => $totalFaturamento,
                'percentual' => $metaMensal > 0 ? round(($totalFaturamento / $metaMensal) * 100, 1) : 0,
                'currency' => true
            ],
            [
                'label' => 'Pedidos Concluidos',
                'meta' => $metaPedidos,
                'realizado' => $totalPedidos,
                'percentual' => $metaPedidos > 0 ? round(($totalPedidos / $metaPedidos) * 100, 1) : 0,
                'currency' => false
            ],
            [
                'label' => 'Ticket Medio',
                'meta' => $metaTicket,
                'realizado' => round($ticketMedio, 2),
                'percentual' => $metaTicket > 0 ? round(($ticketMedio / $metaTicket) * 100, 1) : 0,
                'currency' => true
            ],
        ];

        // Calcula comparativos mensais
        $this->comparativos = [];
        foreach ($this->tableRows as $index => $row) {
            $faturamentoAtual = $row['faturamento'];
            $pedidosAtual = $row['pedidos'];

            $faturamentoAnterior = $index > 0 ? $this->tableRows[$index - 1]['faturamento'] : 0;
            $pedidosAnterior = $index > 0 ? $this->tableRows[$index - 1]['pedidos'] : 0;

            $variacao = $faturamentoAnterior > 0
                ? (($faturamentoAtual - $faturamentoAnterior) / $faturamentoAnterior) * 100
                : ($faturamentoAtual > 0 ? 100 : 0);

            $this->comparativos[] = [
                'mes' => $row['mes'],
                'faturamento' => $faturamentoAtual,
                'faturamento_ant' => $faturamentoAnterior,
                'pedidos' => $pedidosAtual,
                'pedidos_ant' => $pedidosAnterior,
                'variacao' => ($variacao >= 0 ? '+' : '') . number_format($variacao, 1, ',', '.') . '%',
                'positivo' => $variacao >= 0,
            ];
        }

        $this->barMeta = array_fill(0, count($this->faturamento), $metaMensal);
        $this->barRealizado = $this->faturamento;

        $this->dispatch('updateCharts',
            faturamento: $this->faturamento,
            categorias: $this->categorias,
            distribuicao: $this->distribuicao,
            distribuicaoLabels: $this->distribuicaoLabels,
            barMeta: $this->barMeta,
            barRealizado: $this->barRealizado,
        );
    }

    private function calculateTrend(array $values): ?string
    {
        if (count($values) < 2) {
            return null;
        }

        $lastValue = end($values);
        $previousValue = prev($values);

        if ($previousValue == 0) {
            return $lastValue > 0 ? '+100%' : null;
        }

        $percentChange = (($lastValue - $previousValue) / $previousValue) * 100;
        $sign = $percentChange >= 0 ? '+' : '';

        return $sign . number_format($percentChange, 1, ',', '.') . '%';
    }

    public function getFilteredTableRowsProperty(): array
    {
        $rows = collect($this->tableRows);

        if ($this->selectedMonth !== null) {
            $rows = $rows->where('month_index', $this->selectedMonth);
        }

        if ($this->search !== '') {
            $term = mb_strtolower($this->search);

            $rows = $rows->filter(function (array $row) use ($term) {
                $searchable = implode(' ', [
                    $row['mes'] ?? '',
                    (string) ($row['faturamento'] ?? ''),
                    (string) ($row['pedidos'] ?? ''),
                ]);

                return str_contains(mb_strtolower($searchable), $term);
            });
        }

        return $rows->values()->all();
    }

    public function render()
    {
        return view('livewire.dashboard.kpi-report', [
            'rows' => $this->filteredTableRows,
        ]);
    }
}

