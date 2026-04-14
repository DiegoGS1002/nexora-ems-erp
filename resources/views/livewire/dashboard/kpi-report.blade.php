<div class="nx-dashboard-page" wire:poll.10s="refreshData">

    {{-- ── HEADER ────────────────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Indicadores KPI</h1>
            <p class="nx-page-subtitle">Analise de desempenho com graficos e drill-down por periodo.</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('dashboard.index') }}" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar para Visao Geral
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

    {{-- ── FILTROS ─────────────────────────────────────────── --}}
    <div class="nx-filters-bar">
        <div class="nx-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" class="nx-search" placeholder="Buscar por mes, faturamento ou pedidos...">
        </div>
        @if($selectedMonth !== null)
            <div class="nx-filter-actions">
                <button type="button" wire:click="clearMonthFilter" class="nx-btn nx-btn-outline nx-btn-sm">
                    Limpar filtro
                </button>
            </div>
        @endif
    </div>

    {{-- ── GRAFICOS: Linha (2fr) + Donut (1fr) ─────────────── --}}
    <div class="nx-dashboard-grid-charts">
        <div class="nx-card nx-chart-card">
            <div class="nx-chart-header">
                <h3>Evolucao de Faturamento</h3>
                <span class="nx-chart-subtitle">Clique em um ponto para filtrar</span>
            </div>
            <div id="kpi-line-chart" class="nx-chart"></div>
        </div>
        <div class="nx-card nx-chart-card">
            <div class="nx-chart-header">
                <h3>Distribuicao por Categoria</h3>
                <span class="nx-chart-subtitle">Periodo atual</span>
            </div>
            <div id="kpi-donut-chart" class="nx-chart"></div>
        </div>
    </div>

    {{-- ── DESEMPENHO GERAL ────────────────────────────────── --}}
    <div class="nx-card nx-desempenho-card">
        <div class="nx-chart-header" style="padding: 20px 20px 0;">
            <h3>Desempenho Geral</h3>
            <span class="nx-chart-subtitle">Meta vs Realizado</span>
        </div>

        <div class="nx-desempenho-stats">
            @foreach($desempenhoStats as $stat)
                @php
                    $pct = min((float) $stat['percentual'], 100);
                    $color = $pct >= 90 ? '#10B981' : ($pct >= 70 ? '#F59E0B' : '#EF4444');
                    $formMeta      = $stat['currency'] ? 'R$ ' . number_format($stat['meta'], 2, ',', '.') : number_format($stat['meta'], 0, ',', '.');
                    $formRealizado = $stat['currency'] ? 'R$ ' . number_format($stat['realizado'], 2, ',', '.') : number_format($stat['realizado'], 0, ',', '.');
                @endphp
                <div class="nx-desempenho-stat">
                    <div class="nx-desempenho-stat-header">
                        <span class="nx-desempenho-label">{{ $stat['label'] }}</span>
                        <span class="nx-desempenho-pct" style="color: {{ $color }}">{{ number_format($pct, 1, ',', '.') }}%</span>
                    </div>
                    <div class="nx-desempenho-values">
                        <span>Realizado: <strong>{{ $formRealizado }}</strong></span>
                        <span>Meta: {{ $formMeta }}</span>
                    </div>
                    <div class="nx-progress-track">
                        <div class="nx-progress-fill" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="padding: 0 20px 20px;">
            <div id="kpi-bar-chart"></div>
        </div>
    </div>

    {{-- ── COMPARATIVOS MENSAIS ────────────────────────────── --}}
    <div class="nx-card nx-comparativo-card">
        <div class="nx-card-header">
            <h3>Comparativos Mensais</h3>
            <span class="nx-chart-subtitle">Faturamento e pedidos vs mes anterior</span>
        </div>
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th class="nx-th-right">Faturamento</th>
                        <th class="nx-th-right">Mes Anterior</th>
                        <th class="nx-th-center">Variacao</th>
                        <th class="nx-th-center">Pedidos</th>
                        <th class="nx-th-center">Pedidos Ant.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparativos as $comp)
                        <tr>
                            <td><strong>{{ $comp['mes'] }}</strong></td>
                            <td class="nx-td-right">R$ {{ number_format((float) $comp['faturamento'], 2, ',', '.') }}</td>
                            <td class="nx-td-right nx-td-muted">R$ {{ number_format((float) $comp['faturamento_ant'], 2, ',', '.') }}</td>
                            <td class="nx-td-center">
                                <span class="nx-badge {{ $comp['positivo'] ? 'nx-badge-success' : 'nx-badge-danger' }}">
                                    {{ $comp['variacao'] }}
                                </span>
                            </td>
                            <td class="nx-td-center"><strong>{{ $comp['pedidos'] }}</strong></td>
                            <td class="nx-td-center nx-td-muted">{{ $comp['pedidos_ant'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── TABELA DETALHADA ────────────────────────────────── --}}
    <x-dashboard.table :rows="$rows" />

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                let lineChart, donutChart, barChart;

                function initCharts() {
                    const lineEl  = document.querySelector('#kpi-line-chart');
                    const donutEl = document.querySelector('#kpi-donut-chart');
                    const barEl   = document.querySelector('#kpi-bar-chart');
                    if (!lineEl || !donutEl || typeof ApexCharts === 'undefined') return;

                    if (!lineChart) {
                        lineChart = new ApexCharts(lineEl, {
                            chart: {
                                type: 'line', height: 280,
                                toolbar: { show: false }, fontFamily: 'Inter, sans-serif',
                                events: {
                                    dataPointSelection(e, ctx, cfg) {
                                        window.Livewire && window.Livewire.dispatch('filtrarMes', { mes: cfg.dataPointIndex });
                                    }
                                }
                            },
                            stroke: { curve: 'smooth', width: 3 },
                            series: [{ name: 'Faturamento', data: @js($faturamento) }],
                            xaxis: { categories: @js($categorias), labels: { style: { fontSize: '12px' } } },
                            yaxis: { labels: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR'), style: { fontSize: '11px' } } },
                            grid: { strokeDashArray: 4, borderColor: '#E8EEF5' },
                            colors: ['#3B82F6'],
                            markers: { size: 5, hover: { size: 7 } },
                            tooltip: { y: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) } },
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

                    if (barEl && !barChart) {
                        barChart = new ApexCharts(barEl, {
                            chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                            series: [
                                { name: 'Meta',      data: @js($barMeta) },
                                { name: 'Realizado', data: @js($barRealizado) },
                            ],
                            xaxis: { categories: @js($categorias), labels: { style: { fontSize: '12px' } } },
                            yaxis: { labels: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR'), style: { fontSize: '11px' } } },
                            colors: ['#E2E8F0', '#3B82F6'],
                            plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
                            grid: { strokeDashArray: 4, borderColor: '#E8EEF5' },
                            dataLabels: { enabled: false },
                            legend: { position: 'top', fontSize: '12px' },
                            tooltip: { y: { formatter: v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) } },
                        });
                        barChart.render();
                    }
                }

                function updateCharts(p) {
                    initCharts();
                    if (!lineChart || !donutChart || !p) return;
                    lineChart.updateSeries([{ data: p.faturamento ?? [] }]);
                    lineChart.updateOptions({ xaxis: { categories: p.categorias ?? [] } });
                    donutChart.updateSeries(p.distribuicao ?? []);
                    donutChart.updateOptions({ labels: p.distribuicaoLabels ?? [] });
                    if (barChart) {
                        barChart.updateSeries([
                            { name: 'Meta',      data: p.barMeta      ?? [] },
                            { name: 'Realizado', data: p.barRealizado ?? [] },
                        ]);
                        barChart.updateOptions({ xaxis: { categories: p.categorias ?? [] } });
                    }
                }

                window.Livewire.on('updateCharts', ev => {
                    const p = Array.isArray(ev) ? ev[0] : ev;
                    updateCharts(p);
                });

                initCharts();
            });
        </script>
    @endpush

</div>

