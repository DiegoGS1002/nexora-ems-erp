<div class="nx-folha-page">

    {{-- ══════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'rh') }}" class="nx-breadcrumb-link">Recursos Humanos</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Folha de Pagamento</span>
            </nav>
            <h1 class="nx-page-title">Folha de Pagamento</h1>
            <p class="nx-page-subtitle">
                Competência:
                <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $this->referenceMonth)->translatedFormat('F \d\e Y') }}</strong>
            </p>
        </div>
        <div class="nx-page-actions">
            <button type="button"
                wire:click="$set('showGenerateAllModal', true)"
                class="nx-btn nx-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Gerar Todas as Folhas
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="alert-error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,7000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-folha-kpis">

        {{-- Total Proventos --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Proventos</p>
                    <p class="nx-kpi-card-value" style="color:#15803D;font-size:22px;">
                        R$ {{ number_format($kpis['total_earnings'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">
                        {{ $kpis['count_draft'] + $kpis['count_closed'] + $kpis['count_paid'] }} colaborador(es)
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Descontos --}}
        <div class="nx-kpi-card nx-kpi-card--alert">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Descontos</p>
                    <p class="nx-kpi-card-value" style="color:#B91C1C;font-size:22px;">
                        R$ {{ number_format($kpis['total_deductions'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-negative">INSS, IRRF e outros</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
        </div>

        {{-- Líquido a Pagar --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Líquido a Pagar</p>
                    <p class="nx-kpi-card-value" style="color:#1D4ED8;font-size:22px;">
                        R$ {{ number_format($kpis['net_salary'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Valor líquido total da competência</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(29,78,216,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
            </div>
        </div>

        {{-- Status da Folha --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Status das Folhas</p>
                    <div class="nx-folha-status-mini">
                        <span class="nx-folha-status-dot" style="background:#94A3B8;"></span>
                        <span>{{ $kpis['count_draft'] }} Rascunho</span>
                    </div>
                    <div class="nx-folha-status-mini">
                        <span class="nx-folha-status-dot" style="background:#D97706;"></span>
                        <span>{{ $kpis['count_closed'] }} Fechada(s)</span>
                    </div>
                    <div class="nx-folha-status-mini">
                        <span class="nx-folha-status-dot" style="background:#15803D;"></span>
                        <span>{{ $kpis['count_paid'] }} Paga(s)</span>
                    </div>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(100,116,139,0.1);color:#64748B;border-color:rgba(100,116,139,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         TOOLBAR — Month picker
    ══════════════════════════════════════════════════ --}}
    <div class="nx-folha-toolbar">
        <div class="nx-folha-month-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#64748B;flex-shrink:0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input
                type="month"
                wire:model.live="referenceMonth"
                class="nx-filter-select"
                title="Competência"
            >
        </div>
        <p class="nx-folha-toolbar-info">
            {{ $rows->count() }} colaborador(es) ativo(s) ·
            {{ $rows->filter(fn($r) => $r['payroll'] !== null)->count() }} com folha gerada
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Cargo / Dep.</th>
                        <th class="nx-th-right">Salário Base</th>
                        <th class="nx-th-right">Proventos (+)</th>
                        <th class="nx-th-right">Descontos (-)</th>
                        <th class="nx-th-right">Líquido</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            /** @var \App\Models\Employees $emp */
                            $emp     = $row['employee'];
                            /** @var \App\Models\Payroll|null $payroll */
                            $payroll = $row['payroll'];

                            // initials
                            $parts    = explode(' ', trim($emp->name));
                            $initials = strtoupper(substr($parts[0], 0, 1) . (count($parts) > 1 ? substr(end($parts), 0, 1) : ''));
                        @endphp
                        <tr>
                            {{-- Colaborador --}}
                            <td>
                                <div class="nx-folha-emp-cell">
                                    <div class="nx-folha-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="nx-folha-emp-name">{{ $emp->name }}</div>
                                        <div class="nx-folha-emp-code">{{ $emp->internal_code ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Cargo / Dep. --}}
                            <td>
                                <div class="nx-folha-role-cell">
                                    <span class="nx-folha-role">{{ $emp->role ?? '—' }}</span>
                                    @if($emp->department)
                                        <span class="nx-folha-dept">{{ $emp->department }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Salário Base --}}
                            <td class="nx-td-right">
                                <span class="nx-folha-salary">
                                    R$ {{ number_format((float) ($payroll?->base_salary ?? $emp->salary ?? 0), 2, ',', '.') }}
                                </span>
                            </td>

                            {{-- Proventos --}}
                            <td class="nx-td-right">
                                @if($payroll)
                                    <span class="nx-folha-earnings">
                                        + R$ {{ number_format((float) $payroll->total_earnings, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="nx-td-muted">—</span>
                                @endif
                            </td>

                            {{-- Descontos --}}
                            <td class="nx-td-right">
                                @if($payroll)
                                    <span class="nx-folha-deductions">
                                        - R$ {{ number_format((float) $payroll->total_deductions, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="nx-td-muted">—</span>
                                @endif
                            </td>

                            {{-- Líquido --}}
                            <td class="nx-td-right">
                                @if($payroll)
                                    <span class="nx-folha-net">
                                        R$ {{ number_format((float) $payroll->net_salary, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="nx-td-muted">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="nx-td-center">
                                @if($payroll)
                                    <span class="nx-badge {{ $payroll->status->badgeClass() }}">
                                        {{ $payroll->status->label() }}
                                    </span>
                                @else
                                    <span class="nx-badge nx-badge-neutral">Não gerada</span>
                                @endif
                            </td>

                            {{-- Ações --}}
                            <td class="nx-td-actions">
                                <div class="nx-pagar-actions" x-data="{ open: false }" @click.outside="open = false">
                                    <button
                                        type="button"
                                        class="nx-action-btn"
                                        @click="open = !open"
                                        title="Ações"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                    </button>

                                    <div class="nx-pagar-dropdown" x-show="open" x-transition x-cloak>
                                        @if(! $payroll)
                                            <button type="button"
                                                wire:click="generatePayroll('{{ $emp->id }}')"
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item nx-dropdown-pay">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                                Gerar Folha
                                            </button>
                                        @else
                                            <button type="button"
                                                wire:click="openHolerite({{ $payroll->id }})"
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                                Ver Holerite
                                            </button>

                                            @if($payroll->status === \App\Enums\PayrollStatus::Draft)
                                                <button type="button"
                                                    wire:click="openCloseModal({{ $payroll->id }})"
                                                    @click="open = false"
                                                    class="nx-pagar-dropdown-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                    Fechar Folha
                                                </button>
                                            @endif

                                            @if($payroll->status === \App\Enums\PayrollStatus::Closed)
                                                <button type="button"
                                                    wire:click="openPaidModal({{ $payroll->id }})"
                                                    @click="open = false"
                                                    class="nx-pagar-dropdown-item nx-dropdown-pay">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                                    Marcar como Pago
                                                </button>
                                            @endif

                                            <div class="nx-pagar-dropdown-divider"></div>

                                            <button type="button"
                                                wire:click="deletePayroll({{ $payroll->id }})"
                                                wire:confirm="Deseja excluir esta folha? Esta ação não pode ser desfeita."
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item nx-dropdown-delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                                Excluir Folha
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="nx-empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="color:#CBD5E1;margin-bottom:16px"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    <p class="nx-empty-title">Nenhum colaborador ativo</p>
                                    <p class="nx-empty-desc">Cadastre funcionários no módulo de Recursos Humanos para gerar a folha.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="nx-table-footer">
            <span class="nx-table-count">{{ $rows->count() }} colaborador(es)</span>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════
         MODAL — HOLERITE (Visualização e Edição)
    ══════════════════════════════════════════════════ --}}
    @if($showHolerite && $this->currentPayroll)
    @php $cp = $this->currentPayroll; @endphp
    <div class="nx-modal-overlay" wire:click.self="closeHolerite">
        <div class="nx-modal nx-modal--xl" x-data x-trap.noscroll="true">

            {{-- Header --}}
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">Holerite — {{ $cp->employee?->name }}</h2>
                        <p class="nx-modal-subtitle">
                            Competência: {{ $cp->reference_month?->translatedFormat('F/Y') }} ·
                            <span class="nx-badge {{ $cp->status->badgeClass() }}" style="font-size:11px;">{{ $cp->status->label() }}</span>
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="closeHolerite" class="nx-modal-close" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">

                {{-- Summary row --}}
                <div class="nx-folha-holerite-summary">
                    <div class="nx-folha-holerite-sum-item">
                        <span class="nx-folha-holerite-sum-label">Salário Base</span>
                        <span class="nx-folha-holerite-sum-value">R$ {{ number_format((float) $cp->base_salary, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-folha-holerite-sum-item">
                        <span class="nx-folha-holerite-sum-label" style="color:#15803D">Proventos (+)</span>
                        <span class="nx-folha-holerite-sum-value" style="color:#15803D">+ R$ {{ number_format((float) $cp->total_earnings, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-folha-holerite-sum-item">
                        <span class="nx-folha-holerite-sum-label" style="color:#B91C1C">Descontos (-)</span>
                        <span class="nx-folha-holerite-sum-value" style="color:#B91C1C">- R$ {{ number_format((float) $cp->total_deductions, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-folha-holerite-sum-item nx-folha-holerite-sum-net">
                        <span class="nx-folha-holerite-sum-label">Líquido</span>
                        <span class="nx-folha-holerite-sum-value">R$ {{ number_format((float) $cp->net_salary, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- PROVENTOS --}}
                <div class="nx-folha-items-section">
                    <div class="nx-folha-items-header nx-folha-items-header--earning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Proventos
                    </div>

                    @php $earnings = $cp->items->where('type', 'earning'); @endphp
                    @if($earnings->isEmpty())
                        <p class="nx-folha-items-empty">Nenhum provento lançado.</p>
                    @else
                        <div class="nx-folha-items-list">
                            @foreach($earnings as $item)
                                <div class="nx-folha-item-row">
                                    <span class="nx-folha-item-desc">{{ $item->description }}</span>
                                    <span class="nx-folha-item-amount nx-folha-item-amount--earning">
                                        + R$ {{ number_format((float) $item->amount, 2, ',', '.') }}
                                    </span>
                                    @if($cp->status === \App\Enums\PayrollStatus::Draft)
                                        <div class="nx-folha-item-actions">
                                            <button type="button"
                                                wire:click="openItemForm({{ $item->id }})"
                                                class="nx-folha-item-btn" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                            <button type="button"
                                                wire:click="removeItem({{ $item->id }})"
                                                wire:confirm="Remover este item?"
                                                class="nx-folha-item-btn nx-folha-item-btn--danger" title="Remover">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- DESCONTOS --}}
                <div class="nx-folha-items-section">
                    <div class="nx-folha-items-header nx-folha-items-header--deduction">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Descontos
                    </div>

                    @php $deductions = $cp->items->where('type', 'deduction'); @endphp
                    @if($deductions->isEmpty())
                        <p class="nx-folha-items-empty">Nenhum desconto lançado.</p>
                    @else
                        <div class="nx-folha-items-list">
                            @foreach($deductions as $item)
                                <div class="nx-folha-item-row">
                                    <span class="nx-folha-item-desc">{{ $item->description }}</span>
                                    <span class="nx-folha-item-amount nx-folha-item-amount--deduction">
                                        - R$ {{ number_format((float) $item->amount, 2, ',', '.') }}
                                    </span>
                                    @if($cp->status === \App\Enums\PayrollStatus::Draft)
                                        <div class="nx-folha-item-actions">
                                            <button type="button"
                                                wire:click="openItemForm({{ $item->id }})"
                                                class="nx-folha-item-btn" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                            <button type="button"
                                                wire:click="removeItem({{ $item->id }})"
                                                wire:confirm="Remover este item?"
                                                class="nx-folha-item-btn nx-folha-item-btn--danger" title="Remover">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ADD ITEM FORM (only if Draft) --}}
                @if($cp->status === \App\Enums\PayrollStatus::Draft)
                    @if($showItemForm)
                        <div class="nx-folha-item-form">
                            <h4 class="nx-folha-item-form-title">
                                {{ $editingItemId ? 'Editar Verba' : 'Adicionar Verba' }}
                            </h4>
                            <div class="nx-form-grid">
                                <div class="nx-field nx-field--full">
                                    <label>Descrição <span class="nx-required">*</span></label>
                                    <input
                                        type="text"
                                        wire:model="itemDescription"
                                        placeholder="Ex: Horas Extras, INSS, Vale Transporte…"
                                        autocomplete="off"
                                    >
                                    @error('itemDescription') <small class="nx-field-error">{{ $message }}</small> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Tipo <span class="nx-required">*</span></label>
                                    <select wire:model="itemType">
                                        <option value="earning">Provento</option>
                                        <option value="deduction">Desconto</option>
                                    </select>
                                    @error('itemType') <small class="nx-field-error">{{ $message }}</small> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Valor (R$) <span class="nx-required">*</span></label>
                                    <input
                                        type="text"
                                        wire:model="itemAmount"
                                        placeholder="0,00"
                                        inputmode="decimal"
                                    >
                                    @error('itemAmount') <small class="nx-field-error">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="nx-folha-item-form-actions">
                                <button type="button"
                                    wire:click="$set('showItemForm', false)"
                                    class="nx-btn nx-btn-ghost nx-btn-sm">Cancelar</button>
                                <button type="button"
                                    wire:click="saveItem"
                                    wire:loading.attr="disabled"
                                    class="nx-btn nx-btn-primary nx-btn-sm">
                                    <span wire:loading.remove wire:target="saveItem">Salvar</span>
                                    <span wire:loading wire:target="saveItem">Salvando…</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <button type="button"
                            wire:click="openItemForm()"
                            class="nx-folha-add-item-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Verba
                        </button>
                    @endif
                @endif

            </div>{{-- /.nx-modal-body --}}

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeHolerite" class="nx-btn nx-btn-ghost">Fechar</button>
                @if($cp->status === \App\Enums\PayrollStatus::Draft)
                    <button type="button"
                        wire:click="openCloseModal({{ $cp->id }})"
                        class="nx-btn nx-btn-primary"
                        style="background:#D97706;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Fechar Folha
                    </button>
                @elseif($cp->status === \App\Enums\PayrollStatus::Closed)
                    <button type="button"
                        wire:click="openPaidModal({{ $cp->id }})"
                        class="nx-btn nx-btn-primary"
                        style="background:#15803D;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Marcar como Pago
                    </button>
                @endif
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — FECHAR FOLHA
    ══════════════════════════════════════════════════ --}}
    @if($showCloseModal)
    <div class="nx-modal-overlay" wire:click.self="$set('showCloseModal', false)">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(217,119,6,0.1);color:#D97706;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#92400E;">Fechar Folha</h2>
                        <p class="nx-modal-subtitle">Após fechamento não será possível editar os itens.</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('showCloseModal', false)" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-modal-body">
                <p style="font-size:14px;color:#475569;text-align:center;padding:8px 0;">
                    Confirma o fechamento desta folha de pagamento? O status passará para <strong>Fechada</strong>.
                </p>
            </div>
            <div class="nx-modal-footer">
                <button type="button" wire:click="$set('showCloseModal', false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="closePayroll" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#D97706;">
                    <span wire:loading.remove wire:target="closePayroll">Confirmar Fechamento</span>
                    <span wire:loading wire:target="closePayroll">Fechando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — MARCAR COMO PAGO
    ══════════════════════════════════════════════════ --}}
    @if($showPaidModal)
    <div class="nx-modal-overlay" wire:click.self="$set('showPaidModal', false)">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(16,185,129,0.1);color:#10B981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#15803D;">Registrar Pagamento</h2>
                        <p class="nx-modal-subtitle">Informe a data em que o salário foi pago.</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('showPaidModal', false)" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-modal-body">
                <div class="nx-field nx-field--full">
                    <label for="paymentDate">Data do Pagamento <span class="nx-required">*</span></label>
                    <input type="date" id="paymentDate" wire:model="paymentDate">
                    @error('paymentDate') <small class="nx-field-error">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="nx-modal-footer">
                <button type="button" wire:click="$set('showPaidModal', false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="markAsPaid" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#10B981;">
                    <span wire:loading.remove wire:target="markAsPaid">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Confirmar Pagamento
                    </span>
                    <span wire:loading wire:target="markAsPaid">Registrando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — GERAR TODAS AS FOLHAS
    ══════════════════════════════════════════════════ --}}
    @if($showGenerateAllModal)
    <div class="nx-modal-overlay" wire:click.self="$set('showGenerateAllModal', false)">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">Gerar Folhas em Massa</h2>
                        <p class="nx-modal-subtitle">
                            Competência: {{ \Carbon\Carbon::createFromFormat('Y-m', $referenceMonth)->translatedFormat('F/Y') }}
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="$set('showGenerateAllModal', false)" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-modal-body">
                <p style="font-size:14px;color:#475569;text-align:center;padding:8px 0;">
                    Serão geradas folhas (com salário base) para todos os colaboradores ativos que ainda não possuem registro nesta competência.
                </p>
            </div>
            <div class="nx-modal-footer">
                <button type="button" wire:click="$set('showGenerateAllModal', false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="generateAllPayrolls" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="generateAllPayrolls">Gerar Folhas</span>
                    <span wire:loading wire:target="generateAllPayrolls">Gerando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

