<div class="nx-pagar-page">

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
                <span class="nx-breadcrumb-current">Contas a Pagar</span>
            </nav>
            <h1 class="nx-page-title">Contas a Pagar</h1>
            <p class="nx-page-subtitle">Controle de obrigações e fluxos de saída financeira</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Conta
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
    <div class="nx-pagar-kpis">

        {{-- Total a Pagar Hoje --}}
        <div class="nx-kpi-card nx-kpi-card--alert">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Vence Hoje</p>
                    <p class="nx-kpi-card-value" style="color:#B91C1C;font-size:22px;">
                        R$ {{ number_format($kpis['dueToday'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-negative">Requer atenção imediata</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>

        {{-- Vencendo na Semana --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Vence esta Semana</p>
                    <p class="nx-kpi-card-value" style="color:#D97706;font-size:22px;">
                        R$ {{ number_format($kpis['dueWeek'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Próximos 7 dias</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(217,119,6,0.1);color:#D97706;border-color:rgba(217,119,6,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Pendente --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total em Aberto</p>
                    <p class="nx-kpi-card-value" style="color:#1D4ED8;font-size:22px;">
                        R$ {{ number_format($kpis['totalPending'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">
                        @if($kpis['countOverdue'] > 0)
                            <span style="color:#EF4444;">{{ $kpis['countOverdue'] }} vencida(s)</span>
                        @else
                            Sem vencidos
                        @endif
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(29,78,216,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Pago (mês atual) --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Pago este Mês</p>
                    <p class="nx-kpi-card-value" style="color:#15803D;font-size:22px;">
                        R$ {{ number_format($kpis['totalPaid'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">{{ now()->translatedFormat('F Y') }}</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         FILTER TABS + SEARCH
    ══════════════════════════════════════════════════ --}}
    <div class="nx-pagar-toolbar">

        {{-- Status Filter Tabs --}}
        <div class="nx-pagar-tabs">
            <button type="button"
                wire:click="$set('filterStatus', '')"
                class="nx-pagar-tab {{ $filterStatus === '' ? 'nx-pagar-tab--active' : '' }}">
                Todos
            </button>
            <button type="button"
                wire:click="$set('filterStatus', 'pending')"
                class="nx-pagar-tab {{ $filterStatus === 'pending' ? 'nx-pagar-tab--active nx-pagar-tab--pending' : '' }}">
                <span class="nx-pagar-tab-dot nx-dot-pending"></span>
                Pendentes
            </button>
            <button type="button"
                wire:click="$set('filterStatus', 'overdue')"
                class="nx-pagar-tab {{ $filterStatus === 'overdue' ? 'nx-pagar-tab--active nx-pagar-tab--overdue' : '' }}">
                <span class="nx-pagar-tab-dot nx-dot-overdue"></span>
                Vencidos
            </button>
            <button type="button"
                wire:click="$set('filterStatus', 'paid')"
                class="nx-pagar-tab {{ $filterStatus === 'paid' ? 'nx-pagar-tab--active nx-pagar-tab--paid' : '' }}">
                <span class="nx-pagar-tab-dot nx-dot-paid"></span>
                Pagos
            </button>
            <button type="button"
                wire:click="$set('filterStatus', 'cancelled')"
                class="nx-pagar-tab {{ $filterStatus === 'cancelled' ? 'nx-pagar-tab--active nx-pagar-tab--cancelled' : '' }}">
                <span class="nx-pagar-tab-dot nx-dot-cancelled"></span>
                Cancelados
            </button>
        </div>

        {{-- Search + Month filter --}}
        <div class="nx-filters-bar" style="margin-top:0;flex:1;justify-content:flex-end;">
            <div class="nx-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="nx-search"
                    placeholder="Buscar por descrição ou fornecedor…"
                >
            </div>
            <input
                type="month"
                wire:model.live="filterMonth"
                class="nx-filter-select"
                title="Filtrar por mês de vencimento"
            >
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Descrição / Fornecedor</th>
                        <th>Categoria</th>
                        <th class="nx-th-right">Valor</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr class="{{ $account->status === \App\Enums\PayableStatus::Overdue ? 'nx-row-overdue' : '' }}">

                            {{-- Vencimento --}}
                            <td>
                                <div class="nx-pagar-due-cell">
                                    @if($account->due_date_at)
                                        @php
                                            $isToday   = $account->due_date_at->isToday();
                                            $isOverdue = $account->status === \App\Enums\PayableStatus::Overdue;
                                        @endphp
                                        <span class="nx-pagar-due-date {{ $isOverdue ? 'nx-due-overdue' : ($isToday ? 'nx-due-today' : '') }}">
                                            {{ $account->due_date_at->format('d/m/Y') }}
                                        </span>
                                        @if($isToday && $account->status === \App\Enums\PayableStatus::Pending)
                                            <span class="nx-pagar-due-badge">Hoje</span>
                                        @elseif($isOverdue)
                                            <span class="nx-pagar-due-badge nx-due-badge-overdue">
                                                {{ $account->due_date_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="nx-td-muted">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Descrição / Fornecedor --}}
                            <td>
                                <div class="nx-pagar-desc-cell">
                                    <span class="nx-pagar-desc-title">
                                        {{ $account->description_title ?? '—' }}
                                        @if($account->is_recurring)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" title="Recorrente" style="color:#3B82F6;vertical-align:middle;margin-left:4px"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                                        @endif
                                    </span>
                                    @if($account->supplier)
                                        <span class="nx-pagar-desc-sub">{{ $account->supplier->social_name ?? $account->supplier->name }}</span>
                                    @else
                                        <span class="nx-pagar-desc-sub" style="font-style:italic;">Sem fornecedor</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Categoria --}}
                            <td>
                                @if($account->chartOfAccount)
                                    <span class="nx-pagar-category-tag">
                                        {{ $account->chartOfAccount->code }} — {{ $account->chartOfAccount->name }}
                                    </span>
                                @else
                                    <span class="nx-td-muted">—</span>
                                @endif
                            </td>

                            {{-- Valor --}}
                            <td class="nx-td-right">
                                <div class="nx-pagar-amount-cell">
                                    <span class="nx-pagar-amount">
                                        R$ {{ number_format($account->amount ?? 0, 2, ',', '.') }}
                                    </span>
                                    @if($account->status === \App\Enums\PayableStatus::Paid && $account->paid_amount)
                                        <span class="nx-pagar-paid-amount">
                                            Pago: R$ {{ number_format($account->paid_amount, 2, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="nx-td-center">
                                <span class="nx-badge {{ $account->status->badgeClass() }}">
                                    {{ $account->status->label() }}
                                </span>
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
                                        @if($account->status !== \App\Enums\PayableStatus::Paid && $account->status !== \App\Enums\PayableStatus::Cancelled)
                                            <button type="button"
                                                wire:click="openPayment({{ $account->id }})"
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item nx-dropdown-pay">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                Baixar (Pagar)
                                            </button>
                                        @endif

                                        <button type="button"
                                            wire:click="openEdit({{ $account->id }})"
                                            @click="open = false"
                                            class="nx-pagar-dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar
                                        </button>

                                        @if($account->status !== \App\Enums\PayableStatus::Paid && $account->status !== \App\Enums\PayableStatus::Cancelled)
                                            <button type="button"
                                                wire:click="openReschedule({{ $account->id }})"
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                Reprogramar
                                            </button>
                                        @endif

                                        <div class="nx-pagar-dropdown-divider"></div>

                                        @if($account->status !== \App\Enums\PayableStatus::Cancelled)
                                            <button type="button"
                                                wire:click="cancelAccount({{ $account->id }})"
                                                wire:confirm="Deseja cancelar esta conta?"
                                                @click="open = false"
                                                class="nx-pagar-dropdown-item nx-dropdown-cancel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                Cancelar
                                            </button>
                                        @endif

                                        <button type="button"
                                            wire:click="confirmDelete({{ $account->id }})"
                                            @click="open = false"
                                            class="nx-pagar-dropdown-item nx-dropdown-delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="nx-empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="color:#CBD5E1;margin-bottom:16px"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    <p class="nx-empty-title">Nenhuma conta encontrada</p>
                                    <p class="nx-empty-desc">
                                        @if($search || $filterStatus || $filterMonth)
                                            Tente ajustar os filtros de busca.
                                        @else
                                            Cadastre a primeira conta a pagar clicando em <strong>Nova Conta</strong>.
                                        @endif
                                    </p>
                                    @if(!$search && !$filterStatus && !$filterMonth)
                                        <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary nx-btn-sm">
                                            + Nova Conta
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        @if($accounts->hasPages())
            <div class="nx-table-footer">
                <span class="nx-table-count">
                    Exibindo {{ $accounts->firstItem() }}–{{ $accounts->lastItem() }} de {{ $accounts->total() }} registros
                </span>
                <div class="nx-pagination">
                    {{ $accounts->links() }}
                </div>
            </div>
        @else
            <div class="nx-table-footer">
                <span class="nx-table-count">{{ $accounts->total() }} registro(s)</span>
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════
         MODAL — CRIAR / EDITAR CONTA
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-modal-overlay" wire:click.self="closeModal">
        <div class="nx-modal nx-modal--lg" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">{{ $isEditing ? 'Editar Conta' : 'Nova Conta a Pagar' }}</h2>
                        <p class="nx-modal-subtitle">{{ $isEditing ? 'Atualize os dados da obrigação financeira.' : 'Registre uma nova obrigação financeira.' }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="nx-modal-close" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-form-grid">

                    {{-- Descrição --}}
                    <div class="nx-field nx-field--full">
                        <label for="description_title">Descrição <span class="nx-required">*</span></label>
                        <input
                            type="text"
                            id="description_title"
                            wire:model="form.description_title"
                            placeholder="Ex: Aluguel do escritório, Fatura de energia…"
                            autocomplete="off"
                        >
                        @error('form.description_title') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Fornecedor --}}
                    <div class="nx-field">
                        <label for="supplier_id">Fornecedor</label>
                        <select id="supplier_id" wire:model="form.supplier_id">
                            <option value="">— Selecionar fornecedor —</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected($form->supplier_id == $supplier->id)>
                                    {{ $supplier->social_name ?? $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.supplier_id') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Categoria --}}
                    <div class="nx-field">
                        <label for="chart_of_account_id">Categoria (Plano de Contas)</label>
                        <select id="chart_of_account_id" wire:model="form.chart_of_account_id">
                            <option value="">— Selecionar categoria —</option>
                            @foreach($chartAccounts as $cat)
                                <option value="{{ $cat->id }}" @selected($form->chart_of_account_id == $cat->id)>
                                    {{ $cat->code }} — {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.chart_of_account_id') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Valor --}}
                    <div class="nx-field">
                        <label for="amount">Valor (R$) <span class="nx-required">*</span></label>
                        <input
                            type="text"
                            id="amount"
                            wire:model="form.amount"
                            placeholder="0,00"
                            inputmode="decimal"
                        >
                        @error('form.amount') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Data de Vencimento --}}
                    <div class="nx-field">
                        <label for="due_date_at">Data de Vencimento <span class="nx-required">*</span></label>
                        <input
                            type="date"
                            id="due_date_at"
                            wire:model="form.due_date_at"
                        >
                        @error('form.due_date_at') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="nx-field">
                        <label for="status">Status</label>
                        <select id="status" wire:model="form.status">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" @selected($form->status === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.status') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Observação --}}
                    <div class="nx-field nx-field--full">
                        <label for="observation">Observação</label>
                        <textarea
                            id="observation"
                            wire:model="form.observation"
                            rows="2"
                            placeholder="Informações adicionais, número do boleto, etc."
                        ></textarea>
                        @error('form.observation') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Recorrência --}}
                    <div class="nx-field nx-field--full">
                        <div class="nx-pagar-recurring-row">
                            <label class="nx-pagar-recurring-label">
                                <input type="checkbox" wire:model.live="form.is_recurring" id="is_recurring">
                                <span>Conta recorrente (repetir mensalmente)</span>
                            </label>
                            @if($form->is_recurring)
                                <div class="nx-field" style="min-width:180px;margin:0;">
                                    <label for="recurrence_day">Dia de vencimento</label>
                                    <input
                                        type="number"
                                        id="recurrence_day"
                                        wire:model="form.recurrence_day"
                                        placeholder="Ex: 10"
                                        min="1"
                                        max="31"
                                    >
                                    @error('form.recurrence_day') <small class="nx-field-error">{{ $message }}</small> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Salvar Alterações' : 'Cadastrar Conta' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — BAIXA (PAGAMENTO)
    ══════════════════════════════════════════════════ --}}
    @if($showPaymentModal)
    <div class="nx-modal-overlay" wire:click.self="closePaymentModal">
        <div class="nx-modal" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(16,185,129,0.1);color:#10B981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#15803D;">Registrar Pagamento</h2>
                        <p class="nx-modal-subtitle">Informe os dados do pagamento realizado.</p>
                    </div>
                </div>
                <button type="button" wire:click="closePaymentModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-form-grid">

                    <div class="nx-field">
                        <label for="pay_date">Data do Pagamento <span class="nx-required">*</span></label>
                        <input type="date" id="pay_date" wire:model="pay_date">
                        @error('pay_date') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="nx-field">
                        <label for="pay_amount">Valor Pago (R$) <span class="nx-required">*</span></label>
                        <input type="text" id="pay_amount" wire:model="pay_amount" placeholder="0,00" inputmode="decimal">
                        <small style="color:#64748B;">Informe o valor real pago (com juros ou desconto se houver).</small>
                        @error('pay_amount') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="nx-field nx-field--full">
                        <label for="pay_observation">Observação do Pagamento</label>
                        <textarea id="pay_observation" wire:model="pay_observation" rows="2"
                            placeholder="Comprovante, banco utilizado, etc."></textarea>
                        @error('pay_observation') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closePaymentModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="registerPayment" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#10B981;">
                    <span wire:loading.remove wire:target="registerPayment">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Confirmar Pagamento
                    </span>
                    <span wire:loading wire:target="registerPayment">Registrando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — REPROGRAMAR
    ══════════════════════════════════════════════════ --}}
    @if($showRescheduleModal)
    <div class="nx-modal-overlay" wire:click.self="closeRescheduleModal">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">Reprogramar Vencimento</h2>
                        <p class="nx-modal-subtitle">Defina uma nova data de vencimento.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeRescheduleModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-field nx-field--full">
                    <label for="reschedule_date">Nova Data de Vencimento <span class="nx-required">*</span></label>
                    <input type="date" id="reschedule_date" wire:model="reschedule_date">
                    @error('reschedule_date') <small class="nx-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeRescheduleModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="reschedule" class="nx-btn nx-btn-primary">Reprogramar</button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — CONFIRMAR EXCLUSÃO
    ══════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
    <div class="nx-modal-overlay" wire:click.self="cancelDelete">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(239,68,68,0.1);color:#EF4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#B91C1C;">Confirmar Exclusão</h2>
                        <p class="nx-modal-subtitle">Esta ação não poderá ser desfeita.</p>
                    </div>
                </div>
                <button type="button" wire:click="cancelDelete" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <p style="font-size:14px;color:#475569;text-align:center;padding:8px 0;">
                    Tem certeza que deseja excluir permanentemente esta conta a pagar?
                </p>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="cancelDelete" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="delete" wire:loading.attr="disabled" class="nx-btn nx-btn-danger">
                    <span wire:loading.remove wire:target="delete">Excluir Definitivamente</span>
                    <span wire:loading wire:target="delete">Excluindo…</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>


