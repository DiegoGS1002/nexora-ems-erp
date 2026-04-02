<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - Visao Geral')]
class Overview extends Component
{
    public array $kpis = [];

    public array $faturamento = [];

    public array $categorias = [];

    public array $distribuicao = [];

    public array $distribuicaoLabels = [];

    public array $pedidosRecentes = [];

    public array $movimentacoes = [];

    public function mount(): void
    {
        $this->seedVisualData();
        $this->dispatchCharts();
    }

    public function refreshKpis(): void
    {
        $this->seedVisualData();
        $this->dispatchCharts();
    }

    private function seedVisualData(): void
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

        $this->pedidosRecentes = [
            ['id' => '#1042', 'cliente' => 'Tech Solutions Ltda',  'valor' => 4590.00, 'status' => 'Aprovado'],
            ['id' => '#1041', 'cliente' => 'Comercial Norte SA',   'valor' => 1280.50, 'status' => 'Pendente'],
            ['id' => '#1040', 'cliente' => 'Distribuidora Alfa',   'valor' => 7340.00, 'status' => 'Aprovado'],
            ['id' => '#1039', 'cliente' => 'Industria Sul ME',     'valor' =>  920.00, 'status' => 'Cancelado'],
            ['id' => '#1038', 'cliente' => 'Global Imports',       'valor' => 2150.00, 'status' => 'Aprovado'],
        ];

        $this->movimentacoes = [
            ['descricao' => 'Pagamento recebido - Tech Solutions', 'tipo' => 'entrada', 'valor' => 4590.00, 'data' => 'Hoje, 14h32'],
            ['descricao' => 'Compra de insumos - Fornecedor A',    'tipo' => 'saida',   'valor' => 1200.00, 'data' => 'Hoje, 11h15'],
            ['descricao' => 'Pagamento recebido - Comercial Norte','tipo' => 'entrada', 'valor' => 1280.50, 'data' => 'Ontem, 16h45'],
            ['descricao' => 'Despesa operacional - Logistica',     'tipo' => 'saida',   'valor' =>  450.00, 'data' => 'Ontem, 09h00'],
            ['descricao' => 'Pagamento recebido - Global Imports', 'tipo' => 'entrada', 'valor' => 2150.00, 'data' => '01/04, 10h20'],
        ];
    }

    private function dispatchCharts(): void
    {
        $this->dispatch('updateOverviewCharts',
            faturamento: $this->faturamento,
            categorias: $this->categorias,
            distribuicao: $this->distribuicao,
            distribuicaoLabels: $this->distribuicaoLabels,
        );
    }

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}

