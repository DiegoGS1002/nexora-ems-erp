<div class="nx-dashboard-page" wire:poll.10s="refreshKpis">

    {{-- ── HEADER ────────────────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Dashboard</h1>
            <p class="nx-page-subtitle">Visao geral do sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('dashboard.kpi') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Ver Indicadores KPI
            </a>
        </div>
    </div>

    {{-- ── KPI CARDS (4 colunas) ──────────────────────────── --}}
    <div class="nx-dashboard-kpis">
        @foreach($kpis as $kpi)
            <x-dashboard.kpi-card
                :title="$kpi['title']"
                :subtitle="$kpi['subtitle'] ?? null"
                :value="$kpi['value']"
                :currency="$kpi['currency']"
                :trend="$kpi['trend']"
                :icon="$kpi['icon']"
                :iconBg="$kpi['iconBg'] ?? '#EFF6FF'"
                :iconColor="$kpi['iconColor'] ?? '#3B82F6'"
            />
        @endforeach
    </div>

    {{-- ── GRAFICOS: Linha (2fr) + Donut (1fr) ─────────────── --}}
    <div class="nx-dashboard-grid-charts">
        {{-- Grafico de linha --}}
        <div class="nx-card nx-chart-card">
            <div class="nx-chart-header">
                <h3>Faturamento</h3>
                <span class="nx-chart-subtitle">Evolucao mensal</span>
            </div>
            <div id="overview-line-chart" class="nx-chart"></div>
        </div>

        {{-- Grafico donut --}}
        <div class="nx-card nx-chart-card">
            <div class="nx-chart-header">
                <h3>Distribuicao</h3>
                <span class="nx-chart-subtitle">Por categoria</span>
            </div>
            <div id="overview-donut-chart" class="nx-chart"></div>
        </div>
    </div>

    {{-- ── ATIVIDADES RECENTES ─────────────────────────────── --}}
    <div class="nx-activity-row">

        {{-- Pedidos Recentes --}}
        <div class="nx-card nx-activity-panel">
            <div class="nx-activity-panel-header">
                <h3>Pedidos Recentes</h3>
                <a href="{{ route('vendas.pedidos') }}" class="nx-btn nx-btn-outline nx-btn-sm">Ver todos</a>
            </div>
            <div class="nx-activity-list">
                @forelse($pedidosRecentes as $pedido)
                    <div class="nx-activity-item">
                        <div class="nx-activity-item-left">
                            <span class="nx-activity-item-id">{{ $pedido['id'] }}</span>
                            <span class="nx-activity-item-name">{{ $pedido['cliente'] }}</span>
                        </div>
                        <div class="nx-activity-item-right">
                            <span class="nx-activity-item-value">R$ {{ number_format((float) $pedido['valor'], 2, ',', '.') }}</span>
                            @php
                                $badgeClass = match($pedido['status']) {
                                    'Aprovado', 'Entregue', 'Faturado' => 'nx-badge-success',
                                    'Aberto', 'Separação', 'Rascunho' => 'nx-badge-warning',
                                    'Cancelado'                        => 'nx-badge-danger',
                                    default                            => 'nx-badge-neutral',
                                };
                            @endphp
                            <span class="nx-badge {{ $badgeClass }}">{{ $pedido['status'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="nx-activity-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#CBD5E1;margin-bottom:8px"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <span>Nenhum pedido registrado ainda.</span>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Movimentacoes --}}
        <div class="nx-card nx-activity-panel">
            <div class="nx-activity-panel-header">
                <h3>Movimentações</h3>
            </div>
            <div class="nx-activity-list">
                @forelse($movimentacoes as $mov)
                    <div class="nx-activity-item">
                        <div class="nx-activity-item-left">
                            <span class="nx-activity-dot nx-dot-{{ $mov['tipo'] }}"></span>
                            <div class="nx-activity-item-desc">
                                <span class="nx-activity-item-name">{{ $mov['descricao'] }}</span>
                                <span class="nx-activity-item-meta">{{ $mov['data'] }}</span>
                            </div>
                        </div>
                        <div class="nx-activity-item-right">
                            <span class="nx-activity-item-valor nx-val-{{ $mov['tipo'] }}">
                                {{ $mov['tipo'] === 'entrada' ? '+' : '-' }} R$ {{ number_format((float) $mov['valor'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="nx-activity-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#CBD5E1;margin-bottom:8px"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span>Nenhuma movimentação financeira registrada ainda.</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                let lineChart, donutChart;

                function initCharts() {
                    const lineEl  = document.querySelector('#overview-line-chart');
                    const donutEl = document.querySelector('#overview-donut-chart');
                    if (!lineEl || !donutEl || typeof ApexCharts === 'undefined') return;

                    if (!lineChart) {
                        lineChart = new ApexCharts(lineEl, {
                            chart: { type: 'line', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                            stroke: { curve: 'smooth', width: 3 },
                            series: [{ name: 'Faturamento', data: @js($faturamento) }],
                            xaxis: { categories: @js($categorias), labels: { style: { fontSize: '12px' } } },
                            yaxis: { labels: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR'), style: { fontSize: '11px' } } },
                            grid: { strokeDashArray: 4, borderColor: '#E8EEF5' },
                            colors: ['#3B82F6'],
                            tooltip: { y: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) } },
                            markers: { size: 4 },
                        });
                        lineChart.render();
                    }

                    if (!donutChart) {
                        donutChart = new ApexCharts(donutEl, {
                            chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
                            series: @js($distribuicao),
                            labels: @js($distribuicaoLabels),
                            legend: { position: 'bottom', fontSize: '12px' },
                            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                            plotOptions: { pie: { donut: { size: '65%' } } },
                        });
                        donutChart.render();
                    }
                }

                function updateCharts(p) {
                    initCharts();
                    if (!lineChart || !donutChart || !p) return;
                    lineChart.updateSeries([{ data: p.faturamento ?? [] }]);
                    lineChart.updateOptions({ xaxis: { categories: p.categorias ?? [] } });
                    donutChart.updateSeries(p.distribuicao ?? []);
                    donutChart.updateOptions({ labels: p.distribuicaoLabels ?? [] });
                }

                window.Livewire.on('updateOverviewCharts', ev => {
                    const p = Array.isArray(ev) ? ev[0] : ev;
                    updateCharts(p);
                });

                initCharts();
            });
        </script>
    @endpush

</div>

