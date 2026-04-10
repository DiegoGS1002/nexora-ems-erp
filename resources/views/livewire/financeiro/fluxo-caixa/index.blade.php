<div class="nx-fluxo-page">

    {{-- ══════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'financeiro') }}" class="nx-breadcrumb-link">Financeiro</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Fluxo de Caixa</span>
            </nav>
            <h1 class="nx-page-title">Fluxo de Caixa</h1>
            <p class="nx-page-subtitle">Visão consolidada de movimentações e projeções financeiras</p>
        </div>
        <div class="nx-page-actions">
            {{-- Regime de Visualização --}}
            <div class="nx-fluxo-view-toggle">
                <button type="button"
                    wire:click="$set('viewMode', 'caixa')"
                    class="nx-fluxo-view-btn {{ $viewMode === 'caixa' ? 'active' : '' }}"
                    title="Regime de Caixa: mostra valores quando efetivamente recebidos/pagos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Caixa
                </button>
                <button type="button"
                    wire:click="$set('viewMode', 'competencia')"
                    class="nx-fluxo-view-btn {{ $viewMode === 'competencia' ? 'active' : '' }}"
                    title="Regime de Competência: mostra valores pela data de vencimento">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Competência
                </button>
            </div>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════════════
         FILTRO DE PERÍODO
    ══════════════════════════════════════════════════ --}}
    <div class="nx-fluxo-filters">
        <div class="nx-fluxo-period-tabs">
            <button type="button" wire:click="$set('period', 'week')"
                class="nx-fluxo-period-btn {{ $period === 'week' ? 'active' : '' }}">
                Semana
            </button>
            <button type="button" wire:click="$set('period', 'month')"
                class="nx-fluxo-period-btn {{ $period === 'month' ? 'active' : '' }}">
                Mês
            </button>
            <button type="button" wire:click="$set('period', 'quarter')"
                class="nx-fluxo-period-btn {{ $period === 'quarter' ? 'active' : '' }}">
                Trimestre
            </button>
            <button type="button" wire:click="$set('period', 'year')"
                class="nx-fluxo-period-btn {{ $period === 'year' ? 'active' : '' }}">
                Ano
            </button>
        </div>

        <div class="nx-fluxo-date-range">
            <div class="nx-fluxo-date-field">
                <label>De</label>
                <input type="date" wire:model.live="startDate" class="nx-fluxo-date-input">
            </div>
            <span class="nx-fluxo-date-sep">até</span>
            <div class="nx-fluxo-date-field">
                <label>Até</label>
                <input type="date" wire:model.live="endDate" class="nx-fluxo-date-input">
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         KPI CARDS — Saldos
    ══════════════════════════════════════════════════ --}}
    <div class="nx-fluxo-kpis">

        {{-- Saldo Inicial --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Saldo Inicial</p>
                    <p class="nx-kpi-card-value" style="font-size:22px;">
                        R$ {{ number_format($initialBalance, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Contas bancárias ativas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(100,116,139,0.1);color:#64748B;border-color:rgba(100,116,139,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><circle cx="12" cy="12" r="4"/><line x1="2" y1="9" x2="6" y2="9"/><line x1="18" y1="9" x2="22" y2="9"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Entradas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Entradas</p>
                    <p class="nx-kpi-card-value" style="color:#15803D;font-size:22px;">
                        + R$ {{ number_format($totalEntries, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">{{ $viewMode === 'caixa' ? 'Recebimentos efetivos' : 'Previsão de recebimentos' }}</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Saídas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Saídas</p>
                    <p class="nx-kpi-card-value" style="color:#B91C1C;font-size:22px;">
                        - R$ {{ number_format($totalExits, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">{{ $viewMode === 'caixa' ? 'Pagamentos efetivos' : 'Previsão de pagamentos' }}</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                </div>
            </div>
        </div>

        {{-- Saldo Final --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Saldo Final</p>
                    <p class="nx-kpi-card-value" style="color:{{ $finalBalance >= 0 ? '#15803D' : '#B91C1C' }};font-size:22px;">
                        R$ {{ number_format($finalBalance, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend {{ $finalBalance >= 0 ? 'is-positive' : 'is-negative' }}">
                        {{ $finalBalance >= 0 ? 'Saldo positivo no período' : 'Saldo negativo no período' }}
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         GRÁFICO DE LINHA — Entradas vs Saídas
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card nx-fluxo-chart-card">
        <div class="nx-fluxo-chart-header">
            <div class="nx-fluxo-chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <h3>Performance do Fluxo</h3>
            </div>
            <div class="nx-fluxo-chart-legend">
                <span class="nx-fluxo-legend-item">
                    <span class="nx-fluxo-legend-dot" style="background:#10B981"></span>
                    Entradas
                </span>
                <span class="nx-fluxo-legend-item">
                    <span class="nx-fluxo-legend-dot" style="background:#EF4444"></span>
                    Saídas
                </span>
            </div>
        </div>
        <div class="nx-fluxo-chart-container" wire:ignore>
            <canvas id="fluxoCaixaChart"></canvas>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         PROJEÇÕES — A Receber / A Pagar / Projetado
    ══════════════════════════════════════════════════ --}}
    <div class="nx-fluxo-projection-row">

        {{-- A Receber (Pendente) --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">A Receber (Pendente)</p>
                    <p class="nx-kpi-card-value" style="color:#15803D;font-size:20px;">
                        R$ {{ number_format($pendingReceivables, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">Contas pendentes no período</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
            </div>
        </div>

        {{-- A Pagar (Pendente) --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">A Pagar (Pendente)</p>
                    <p class="nx-kpi-card-value" style="color:#B91C1C;font-size:20px;">
                        R$ {{ number_format($pendingPayables, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-negative">Contas pendentes no período</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>

        {{-- Saldo Projetado --}}
        <div class="nx-kpi-card nx-fluxo-projected-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Saldo Projetado</p>
                    <p class="nx-kpi-card-value" style="color:{{ $projectedBalance >= 0 ? '#15803D' : '#B91C1C' }};font-size:20px;">
                        R$ {{ number_format($projectedBalance, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend {{ $projectedBalance >= 0 ? 'is-positive' : 'is-negative' }}">
                        Se tudo for pago/recebido
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.15);color:#3B82F6;border-color:rgba(59,130,246,0.25)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         TABELA DIÁRIA
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card">
        <div class="nx-fluxo-table-header">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Movimentação Diária
            </h3>
            <span class="nx-fluxo-table-period">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </span>
        </div>

        <div class="nx-table-wrap">
            <table class="nx-table nx-fluxo-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="nx-th-right nx-th-entradas">Entradas (R$)</th>
                        <th class="nx-th-right nx-th-saidas">Saídas (R$)</th>
                        <th class="nx-th-right">Saldo Acumulado (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyFlow as $row)
                        <tr class="{{ $row['is_today'] ? 'nx-row-today' : '' }} {{ $row['is_future'] ? 'nx-row-future' : '' }}">
                            <td>
                                <div class="nx-fluxo-date-cell">
                                    <span class="nx-fluxo-date-main">{{ $row['date'] }}</span>
                                    <span class="nx-fluxo-date-day">{{ $row['day_name'] }}</span>
                                    @if($row['is_today'])
                                        <span class="nx-fluxo-badge-today">Hoje</span>
                                    @elseif($row['is_future'])
                                        <span class="nx-fluxo-badge-future">Projetado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="nx-td-right">
                                @if($row['entradas'] > 0)
                                    <span class="nx-fluxo-value-positive">+ {{ number_format($row['entradas'], 2, ',', '.') }}</span>
                                @else
                                    <span class="nx-fluxo-muted">—</span>
                                @endif
                            </td>
                            <td class="nx-td-right">
                                @if($row['saidas'] > 0)
                                    <span class="nx-fluxo-value-negative">- {{ number_format($row['saidas'], 2, ',', '.') }}</span>
                                @else
                                    <span class="nx-fluxo-muted">—</span>
                                @endif
                            </td>
                            <td class="nx-td-right">
                                <span class="nx-fluxo-saldo {{ $row['saldo'] >= 0 ? 'nx-fluxo-saldo-positive' : 'nx-fluxo-saldo-negative' }}">
                                    {{ number_format($row['saldo'], 2, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="nx-empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:#CBD5E1;margin-bottom:12px"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <p class="nx-empty-title">Nenhuma movimentação</p>
                                    <p class="nx-empty-desc">Não há movimentações financeiras no período selecionado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@script
<script>
let fluxoChartInstance = null;

function initFluxoChart() {
    const ctx = document.getElementById('fluxoCaixaChart');
    if (!ctx) return;

    if (fluxoChartInstance) {
        fluxoChartInstance.destroy();
        fluxoChartInstance = null;
    }

    const chartData = @json($chartData);

    fluxoChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Entradas',
                    data: chartData.entradas,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                },
                {
                    label: 'Saídas',
                    data: chartData.saidas,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#EF4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleColor: '#F8FAFC',
                    bodyColor: '#CBD5E1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: '#94A3B8',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    border: { display: false, dash: [4, 4] },
                    ticks: {
                        color: '#94A3B8',
                        font: { size: 11 },
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}

initFluxoChart();

$wire.on('chartDataUpdated', () => {
    setTimeout(initFluxoChart, 100);
});
</script>
@endscript

