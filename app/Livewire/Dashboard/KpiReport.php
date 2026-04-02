<?php

namespace App\Livewire\Dashboard;

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
        $this->kpis = [
            [
                'title'     => 'Faturamento',
                'value'     => 128590,
                'currency'  => true,
                'iconBg'    => '#EFF6FF',
                'iconColor' => '#3B82F6',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
                'trend'     => '+15,7%',
            ],
            [
                'title'     => 'Produtos',
                'value'     => 5284,
                'currency'  => false,
                'iconBg'    => '#F5F3FF',
                'iconColor' => '#7C3AED',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
                'trend'     => '+2,1%',
            ],
            [
                'title'     => 'Pedidos',
                'value'     => 72,
                'currency'  => false,
                'iconBg'    => '#FFFBEB',
                'iconColor' => '#D97706',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
                'trend'     => '+3,5%',
            ],
            [
                'title'     => 'Despesas',
                'value'     => 78445,
                'currency'  => true,
                'iconBg'    => '#FFF1F2',
                'iconColor' => '#E11D48',
                'icon'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
                'trend'     => '-8,3%',
            ],
        ];

        $this->faturamento = [12000, 19000, 30000, 50000];
        $this->categorias = ['Jan', 'Fev', 'Mar', 'Abr'];
        $this->distribuicao = [34, 28, 20, 18];
        $this->distribuicaoLabels = ['Comercio', 'Construcao', 'Servicos', 'Tecnologia'];

        $orders = [22, 41, 58, 72];
        $this->tableRows = collect($this->categorias)
            ->values()
            ->map(fn (string $mes, int $index) => [
                'month_index' => $index,
                'mes' => $mes,
                'faturamento' => $this->faturamento[$index] ?? 0,
                'pedidos' => $orders[$index] ?? 0,
            ])
            ->all();

        $this->desempenhoStats = [
            ['label' => 'Meta Mensal',         'meta' => 150000, 'realizado' => 128590, 'percentual' => 85.7, 'currency' => true],
            ['label' => 'Pedidos Concluidos',   'meta' => 100,    'realizado' => 72,     'percentual' => 72.0, 'currency' => false],
            ['label' => 'Ticket Medio',         'meta' => 2000,   'realizado' => 1786,   'percentual' => 89.3, 'currency' => true],
        ];

        $this->comparativos = [
            ['mes' => 'Jan', 'faturamento' => 12000, 'faturamento_ant' =>  9500, 'pedidos' => 22, 'pedidos_ant' => 18, 'variacao' => '+26,3%', 'positivo' => true],
            ['mes' => 'Fev', 'faturamento' => 19000, 'faturamento_ant' => 14000, 'pedidos' => 41, 'pedidos_ant' => 28, 'variacao' => '+35,7%', 'positivo' => true],
            ['mes' => 'Mar', 'faturamento' => 30000, 'faturamento_ant' => 27000, 'pedidos' => 58, 'pedidos_ant' => 52, 'variacao' => '+11,1%', 'positivo' => true],
            ['mes' => 'Abr', 'faturamento' => 50000, 'faturamento_ant' => 30000, 'pedidos' => 72, 'pedidos_ant' => 50, 'variacao' => '+66,7%', 'positivo' => true],
        ];

        $this->barMeta      = [150000, 150000, 150000, 150000];
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

