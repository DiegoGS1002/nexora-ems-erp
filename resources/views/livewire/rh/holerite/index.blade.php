<div class="nx-holerite-page">

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
                <span class="nx-breadcrumb-current">Holerite</span>
            </nav>
            <h1 class="nx-page-title">Holerite</h1>
            <p class="nx-page-subtitle">
                Visualize e imprima comprovantes de pagamento por colaborador e competência
            </p>
        </div>
        <div class="nx-page-actions">
            @if($selectedPayroll)
                <a
                    href="{{ route('holerite.print', $selectedPayroll->id) }}"
                    target="_blank"
                    rel="noopener"
                    class="nx-btn nx-btn-secondary nx-holerite-print-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Imprimir / PDF
                </a>
            @endif
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
         KPI STRIP
    ══════════════════════════════════════════════════ --}}
    <div class="nx-holerite-kpis nx-no-print">
        <div class="nx-holerite-kpi-item">
            <span class="nx-holerite-kpi-label">Total Folhas</span>
            <span class="nx-holerite-kpi-value" style="color:#3B82F6">{{ $kpis['count_total'] }}</span>
        </div>
        <div class="nx-holerite-kpi-sep"></div>
        <div class="nx-holerite-kpi-item">
            <span class="nx-holerite-kpi-label">Rascunho</span>
            <span class="nx-holerite-kpi-value" style="color:#64748B">{{ $kpis['count_draft'] }}</span>
        </div>
        <div class="nx-holerite-kpi-sep"></div>
        <div class="nx-holerite-kpi-item">
            <span class="nx-holerite-kpi-label">Fechadas</span>
            <span class="nx-holerite-kpi-value" style="color:#D97706">{{ $kpis['count_closed'] }}</span>
        </div>
        <div class="nx-holerite-kpi-sep"></div>
        <div class="nx-holerite-kpi-item">
            <span class="nx-holerite-kpi-label">Pagas</span>
            <span class="nx-holerite-kpi-value" style="color:#15803D">{{ $kpis['count_paid'] }}</span>
        </div>
        <div class="nx-holerite-kpi-sep"></div>
        <div class="nx-holerite-kpi-item">
            <span class="nx-holerite-kpi-label">Líquido Total</span>
            <span class="nx-holerite-kpi-value" style="color:#1D4ED8">
                R$ {{ number_format($kpis['total_net_salary'], 2, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MAIN LAYOUT: SIDEBAR + DOCUMENT
    ══════════════════════════════════════════════════ --}}
    <div class="nx-holerite-layout">

        {{-- ──────────── SIDEBAR ──────────── --}}
        <aside class="nx-holerite-sidebar nx-no-print">

            {{-- Filtros --}}
            <div class="nx-holerite-sidebar-filters">
                <div class="nx-holerite-sidebar-filter-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#64748B;flex-shrink:0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <input
                        type="month"
                        wire:model.live="referenceMonth"
                        class="nx-holerite-filter-input"
                        title="Competência"
                    >
                </div>
                <div class="nx-holerite-sidebar-filter-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#64748B;flex-shrink:0"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchEmployee"
                        placeholder="Buscar colaborador…"
                        class="nx-holerite-filter-input"
                    >
                </div>
                <div class="nx-holerite-sidebar-filter-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#64748B;flex-shrink:0"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41"/><path d="M4.93 4.93l1.41 1.41"/><path d="M19.07 19.07l-1.41-1.41"/><path d="M4.93 19.07l1.41-1.41"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>
                    <select wire:model.live="filterStatus" class="nx-holerite-filter-input">
                        <option value="">Todos os status</option>
                        <option value="draft">Rascunho</option>
                        <option value="closed">Fechada</option>
                        <option value="paid">Paga</option>
                    </select>
                </div>
            </div>

            {{-- Lista de folhas --}}
            <div class="nx-holerite-list">
                <div class="nx-holerite-list-header">
                    <span>{{ $payrollList->count() }} {{ $payrollList->count() === 1 ? 'registro' : 'registros' }}</span>
                </div>

                @forelse($payrollList as $payroll)
                    @php
                        $emp      = $payroll->employee;
                        $parts    = explode(' ', trim($emp?->name ?? ''));
                        $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (count($parts) > 1 ? substr(end($parts), 0, 1) : ''));
                        $isActive = $selectedPayroll?->id === $payroll->id;
                    @endphp
                    <button
                        type="button"
                        wire:click="selectPayroll({{ $payroll->id }})"
                        class="nx-holerite-list-item {{ $isActive ? 'is-active' : '' }}"
                    >
                        <div class="nx-holerite-list-avatar">{{ $initials }}</div>
                        <div class="nx-holerite-list-info">
                            <span class="nx-holerite-list-name">{{ $emp?->name ?? '—' }}</span>
                            <span class="nx-holerite-list-meta">
                                {{ $emp?->role ?? '—' }}
                                @if($emp?->department) · {{ $emp->department }} @endif
                            </span>
                            <div class="nx-holerite-list-values">
                                <span class="nx-holerite-list-net">
                                    R$ {{ number_format((float) $payroll->net_salary, 2, ',', '.') }}
                                </span>
                                <span class="nx-badge {{ $payroll->status->badgeClass() }}" style="font-size:10px;padding:2px 7px;">
                                    {{ $payroll->status->label() }}
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="nx-holerite-list-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" style="color:#CBD5E1;margin-bottom:12px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <p>Nenhuma folha encontrada para esta competência.</p>
                        <a href="{{ route('payroll.index') }}" class="nx-btn nx-btn-secondary" style="margin-top:12px;font-size:12px;padding:6px 14px;">
                            Ir para Folha de Pagamento
                        </a>
                    </div>
                @endforelse
            </div>

        </aside>

        {{-- ──────────── DOCUMENT AREA ──────────── --}}
        <main class="nx-holerite-content">

            @if(! $selectedPayroll)
                {{-- EMPTY STATE --}}
                <div class="nx-holerite-empty nx-no-print">
                    <div class="nx-holerite-empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <h2 class="nx-holerite-empty-title">Selecione um colaborador</h2>
                    <p class="nx-holerite-empty-desc">
                        Escolha um colaborador na lista ao lado para visualizar o holerite.
                        @if($payrollList->isEmpty())
                            Gere as folhas primeiro na página de <a href="{{ route('payroll.index') }}" style="color:#3B82F6;text-decoration:underline;">Folha de Pagamento</a>.
                        @endif
                    </p>
                </div>
            @else
                @php
                    $cp  = $selectedPayroll;
                    $emp = $cp->employee;

                    // Proventos e descontos
                    $earnings   = $cp->items->where('type', 'earning');
                    $deductions = $cp->items->where('type', 'deduction');

                    // Totais
                    $totalEarnings   = (float) $cp->total_earnings;
                    $totalDeductions = (float) $cp->total_deductions;
                    $baseSalary      = (float) $cp->base_salary;
                    $netSalary       = (float) $cp->net_salary;

                    // Bases de cálculo estimadas
                    $baseInss = $baseSalary + $totalEarnings;
                    $baseFgts = $baseInss;
                    $baseIrrf = $baseInss - $totalDeductions;

                    // Empresa
                    $company = $companyData;
                @endphp

                {{-- ACTIONS BAR (no print) --}}
                <div class="nx-holerite-actions-bar nx-no-print">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="nx-badge {{ $cp->status->badgeClass() }}">{{ $cp->status->label() }}</span>
                        @if($cp->payment_date)
                            <span style="font-size:12px;color:#64748B;">
                                Pago em {{ $cp->payment_date->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($cp->status === \App\Enums\PayrollStatus::Draft)
                            <button type="button"
                                wire:click="openCloseModal({{ $cp->id }})"
                                class="nx-btn nx-btn-secondary"
                                style="background:rgba(217,119,6,0.08);color:#D97706;border-color:rgba(217,119,6,0.3);font-size:12px;padding:6px 14px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Fechar Folha
                            </button>
                        @endif
                        @if($cp->status === \App\Enums\PayrollStatus::Closed)
                            <button type="button"
                                wire:click="openPaidModal({{ $cp->id }})"
                                class="nx-btn nx-btn-secondary"
                                style="background:rgba(21,128,61,0.08);color:#15803D;border-color:rgba(21,128,61,0.3);font-size:12px;padding:6px 14px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Marcar como Pago
                            </button>
                        @endif
                        <a href="{{ route('holerite.print', $cp->id) }}"
                            target="_blank"
                            rel="noopener"
                            class="nx-btn nx-btn-primary"
                            style="font-size:12px;padding:6px 14px;text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Imprimir / PDF
                        </a>
                    </div>
                </div>

                {{-- ════════════════════════════════════
                     DOCUMENTO HOLERITE (printable)
                ════════════════════════════════════ --}}
                <div class="nx-holerite-doc" id="nx-holerite-doc">

                    {{-- TOPO: empresa + título --}}
                    <div class="nx-holerite-doc-header">
                        <div class="nx-holerite-doc-company">
                            <div class="nx-holerite-doc-company-logo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            </div>
                            <div>
                                <div class="nx-holerite-doc-company-name">{{ $company['name'] }}</div>
                                @if($company['cnpj'])
                                    <div class="nx-holerite-doc-company-cnpj">CNPJ: {{ $company['cnpj'] }}</div>
                                @endif
                                @if($company['address'])
                                    <div class="nx-holerite-doc-company-address">
                                        {{ $company['address'] }}
                                        @if($company['city']) — {{ $company['city'] }}@endif
                                        @if($company['state'])/{{ $company['state'] }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="nx-holerite-doc-title-wrap">
                            <div class="nx-holerite-doc-title">RECIBO DE PAGAMENTO DE SALÁRIO</div>
                            <div class="nx-holerite-doc-subtitle">
                                Competência: {{ $cp->reference_month?->translatedFormat('F/Y') }}
                            </div>
                            @if($cp->payment_date)
                                <div class="nx-holerite-doc-subtitle">
                                    Data de Pagamento: {{ $cp->payment_date->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- DADOS DO FUNCIONÁRIO --}}
                    <div class="nx-holerite-doc-employee">
                        <div class="nx-holerite-doc-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Dados do Colaborador
                        </div>
                        <div class="nx-holerite-doc-emp-grid">
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Nome</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->name ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">CPF</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->identification_number ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Matrícula</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->internal_code ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Cargo</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->role ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Departamento</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->department ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Admissão</span>
                                <span class="nx-holerite-doc-field-value">
                                    {{ $emp?->admission_date?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Jornada</span>
                                <span class="nx-holerite-doc-field-value">{{ $emp?->work_schedule ?? '—' }}</span>
                            </div>
                            <div class="nx-holerite-doc-field">
                                <span class="nx-holerite-doc-field-label">Salário Base</span>
                                <span class="nx-holerite-doc-field-value" style="font-weight:700;color:#1E40AF;">
                                    R$ {{ number_format($baseSalary, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- TABELA DE EVENTOS --}}
                    <div class="nx-holerite-doc-events">
                        <div class="nx-holerite-doc-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                            Demonstrativo de Pagamento
                        </div>

                        <div class="nx-holerite-doc-events-grid">

                            {{-- PROVENTOS --}}
                            <div class="nx-holerite-doc-events-col">
                                <div class="nx-holerite-doc-events-col-header nx-holerite-doc-events-col-header--earning">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Proventos
                                </div>
                                <table class="nx-holerite-doc-events-table">
                                    <thead>
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Descrição</th>
                                            <th class="nx-holerite-th-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="nx-holerite-tr-base">
                                            <td>001</td>
                                            <td>Salário Base</td>
                                            <td class="nx-holerite-td-right nx-holerite-earning">
                                                R$ {{ number_format($baseSalary, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        @forelse($earnings as $idx => $item)
                                            <tr>
                                                <td>{{ str_pad($idx + 2, 3, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td class="nx-holerite-td-right nx-holerite-earning">
                                                    R$ {{ number_format((float) $item->amount, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="nx-holerite-td-empty">Nenhum provento adicional</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="nx-holerite-tfoot-total">
                                            <td colspan="2">Total Proventos</td>
                                            <td class="nx-holerite-td-right">
                                                R$ {{ number_format($baseSalary + $totalEarnings, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- DESCONTOS --}}
                            <div class="nx-holerite-doc-events-col">
                                <div class="nx-holerite-doc-events-col-header nx-holerite-doc-events-col-header--deduction">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Descontos
                                </div>
                                <table class="nx-holerite-doc-events-table">
                                    <thead>
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Descrição</th>
                                            <th class="nx-holerite-th-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($deductions as $idx => $item)
                                            <tr>
                                                <td>{{ str_pad($idx + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td class="nx-holerite-td-right nx-holerite-deduction">
                                                    R$ {{ number_format((float) $item->amount, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="nx-holerite-td-empty">Nenhum desconto lançado</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="nx-holerite-tfoot-total">
                                            <td colspan="2">Total Descontos</td>
                                            <td class="nx-holerite-td-right">
                                                R$ {{ number_format($totalDeductions, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>{{-- /.nx-holerite-doc-events-grid --}}
                    </div>

                    {{-- BASES DE CÁLCULO --}}
                    <div class="nx-holerite-doc-bases">
                        <div class="nx-holerite-doc-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            Bases de Cálculo
                        </div>
                        <div class="nx-holerite-doc-bases-grid">
                            <div class="nx-holerite-doc-base-item">
                                <span class="nx-holerite-doc-base-label">Base INSS</span>
                                <span class="nx-holerite-doc-base-value">R$ {{ number_format($baseInss, 2, ',', '.') }}</span>
                            </div>
                            <div class="nx-holerite-doc-base-item">
                                <span class="nx-holerite-doc-base-label">Base FGTS</span>
                                <span class="nx-holerite-doc-base-value">R$ {{ number_format($baseFgts, 2, ',', '.') }}</span>
                            </div>
                            <div class="nx-holerite-doc-base-item">
                                <span class="nx-holerite-doc-base-label">Base IRRF</span>
                                <span class="nx-holerite-doc-base-value">R$ {{ number_format($baseIrrf, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- TOTAIS / LÍQUIDO --}}
                    <div class="nx-holerite-doc-totals">
                        <div class="nx-holerite-doc-total-row">
                            <span class="nx-holerite-doc-total-label">Total de Vencimentos</span>
                            <span class="nx-holerite-doc-total-value nx-holerite-doc-total-earning">
                                R$ {{ number_format($baseSalary + $totalEarnings, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="nx-holerite-doc-total-row">
                            <span class="nx-holerite-doc-total-label">Total de Descontos</span>
                            <span class="nx-holerite-doc-total-value nx-holerite-doc-total-deduction">
                                R$ {{ number_format($totalDeductions, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="nx-holerite-doc-total-row nx-holerite-doc-total-net-row">
                            <span class="nx-holerite-doc-total-label">Salário Líquido</span>
                            <span class="nx-holerite-doc-total-value nx-holerite-doc-total-net">
                                R$ {{ number_format($netSalary, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- ASSINATURA --}}
                    <div class="nx-holerite-doc-signature">
                        <div class="nx-holerite-doc-sig-item">
                            <div class="nx-holerite-doc-sig-line"></div>
                            <span>{{ $company['name'] }}</span>
                            <span style="font-size:10px;color:#94A3B8">Empregador</span>
                        </div>
                        <div class="nx-holerite-doc-sig-item">
                            <div class="nx-holerite-doc-sig-line"></div>
                            <span>{{ $emp?->name ?? '—' }}</span>
                            <span style="font-size:10px;color:#94A3B8">Colaborador</span>
                        </div>
                    </div>

                    {{-- OBSERVAÇÃO (rodapé legal) --}}
                    <div class="nx-holerite-doc-footer">
                        <p>Este recibo de pagamento é emitido conforme a Consolidação das Leis do Trabalho (CLT) e demais legislações trabalhistas vigentes. Competência: {{ $cp->reference_month?->translatedFormat('F/Y') }}.</p>
                        @if($cp->observations)
                            <p style="margin-top:6px;"><strong>Obs.:</strong> {{ $cp->observations }}</p>
                        @endif
                    </div>

                    {{-- ── BARRA DE IMPRESSÃO (oculta no print) ── --}}
                    <div class="nx-holerite-print-bar nx-no-print">
                        <div class="nx-holerite-print-bar-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>
                                Holerite de <strong>{{ $emp?->name ?? '—' }}</strong>
                                — Competência <strong>{{ $cp->reference_month?->translatedFormat('F/Y') }}</strong>
                                — Líquido: <strong>R$ {{ number_format($netSalary, 2, ',', '.') }}</strong>
                            </span>
                        </div>
                        <a
                            href="{{ route('holerite.print', $cp->id) }}"
                            target="_blank"
                            rel="noopener"
                            class="nx-holerite-print-bar-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Imprimir / Salvar PDF
                        </a>
                    </div>

                </div>{{-- /.nx-holerite-doc --}}

                {{-- ══════════════════════════════════════
                     PAINEL DE EDIÇÃO (apenas Draft, sem print)
                ══════════════════════════════════════ --}}
                @if($cp->status === \App\Enums\PayrollStatus::Draft)
                    <div class="nx-holerite-edit-panel nx-no-print">
                        <div class="nx-holerite-edit-panel-header">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <span>Edição de Verbas</span>
                                <span class="nx-badge nx-badge-neutral" style="font-size:10px;">Rascunho</span>
                            </div>
                            <button type="button"
                                wire:click="openItemForm()"
                                class="nx-btn nx-btn-primary"
                                style="font-size:12px;padding:6px 14px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Adicionar Verba
                            </button>
                        </div>

                        @if($showItemForm)
                            <div class="nx-holerite-edit-form">
                                <h4 style="font-size:13px;font-weight:700;color:#1E293B;margin-bottom:12px;text-transform:uppercase;letter-spacing:0.3px;">
                                    {{ $editingItemId ? 'Editar Verba' : 'Nova Verba' }}
                                </h4>
                                <div class="nx-form-grid">
                                    <div class="nx-field nx-field--full">
                                        <label>Descrição <span class="nx-required">*</span></label>
                                        <input type="text" wire:model="itemDescription" placeholder="Ex: Horas Extras, INSS, Vale Transporte…" autocomplete="off">
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
                                        <input type="text" wire:model="itemAmount" placeholder="0,00" inputmode="decimal">
                                        @error('itemAmount') <small class="nx-field-error">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
                                    <button type="button" wire:click="$set('showItemForm',false)" class="nx-btn nx-btn-ghost nx-btn-sm">Cancelar</button>
                                    <button type="button" wire:click="saveItem" wire:loading.attr="disabled" class="nx-btn nx-btn-primary nx-btn-sm">
                                        <span wire:loading.remove wire:target="saveItem">Salvar</span>
                                        <span wire:loading wire:target="saveItem">Salvando…</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Lista de itens editáveis --}}
                        <div class="nx-holerite-edit-items">
                            @if($cp->items->isEmpty())
                                <p style="font-size:13px;color:#94A3B8;text-align:center;padding:18px;">Nenhuma verba adicionada ainda.</p>
                            @else
                                @foreach($cp->items as $item)
                                    <div class="nx-holerite-edit-item-row">
                                        <span class="nx-badge {{ $item->type === 'earning' ? 'nx-badge-success' : 'nx-badge-danger' }}" style="font-size:10px;min-width:72px;text-align:center;">
                                            {{ $item->type === 'earning' ? 'Provento' : 'Desconto' }}
                                        </span>
                                        <span style="flex:1;font-size:13.5px;font-weight:500;color:#334155;">{{ $item->description }}</span>
                                        <span style="font-size:14px;font-weight:700;color:{{ $item->type === 'earning' ? '#15803D' : '#B91C1C' }};font-family:'Courier New',monospace;margin-right:12px;">
                                            {{ $item->type === 'earning' ? '+' : '-' }} R$ {{ number_format((float) $item->amount, 2, ',', '.') }}
                                        </span>
                                        <div style="display:flex;gap:6px;">
                                            <button type="button" wire:click="openItemForm({{ $item->id }})" class="nx-folha-item-btn" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                            <button type="button" wire:click="removeItem({{ $item->id }})" wire:confirm="Remover este item?" class="nx-folha-item-btn nx-folha-item-btn--danger" title="Remover">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>
                @endif

            @endif
        </main>

    </div>{{-- /.nx-holerite-layout --}}


    {{-- ══════════════════════════════════════════════════
         MODAL — FECHAR FOLHA
    ══════════════════════════════════════════════════ --}}
    @if($showCloseModal)
    <div class="nx-modal-overlay nx-no-print" wire:click.self="$set('showCloseModal', false)">
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
                    Confirma o fechamento desta folha? O status passará para <strong>Fechada</strong>.
                </p>
            </div>
            <div class="nx-modal-footer">
                <button type="button" wire:click="$set('showCloseModal', false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="closePayroll" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#D97706;">
                    <span wire:loading.remove wire:target="closePayroll">Confirmar</span>
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
    <div class="nx-modal-overlay nx-no-print" wire:click.self="$set('showPaidModal', false)">
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
                    <label for="paymentDateHolerite">Data do Pagamento <span class="nx-required">*</span></label>
                    <input type="date" id="paymentDateHolerite" wire:model="paymentDate">
                    @error('paymentDate') <small class="nx-field-error">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="nx-modal-footer">
                <button type="button" wire:click="$set('showPaidModal', false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="markAsPaid" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#10B981;">
                    <span wire:loading.remove wire:target="markAsPaid">Confirmar Pagamento</span>
                    <span wire:loading wire:target="markAsPaid">Registrando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

