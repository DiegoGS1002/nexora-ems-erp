<div class="nx-bancaria-page">

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
                <span class="nx-breadcrumb-current">Contas Bancárias</span>
            </nav>
            <h1 class="nx-page-title">Contas Bancárias</h1>
            <p class="nx-page-subtitle">Gerencie contas correntes, poupanças, caixas e carteiras digitais</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openTransfer" class="nx-btn nx-btn-transfer">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Transferência
            </button>
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
         KPI SUMMARY ROW
    ══════════════════════════════════════════════════ --}}
    <div class="nx-bancaria-kpis">

        {{-- Saldo Total --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Saldo Total</p>
                    <p class="nx-kpi-card-value" style="color:{{ $totalBalance >= 0 ? '#0F172A' : '#EF4444' }};font-size:22px;">
                        R$ {{ number_format($totalBalance, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Todas as contas ativas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                </div>
            </div>
        </div>

        {{-- Saldo Previsto --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Saldo Previsto</p>
                    <p class="nx-kpi-card-value" style="color:#3B82F6;font-size:22px;">
                        R$ {{ number_format($totalPredicted, 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Saldo conciliado</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
            </div>
        </div>

        {{-- Contas Ativas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Contas Ativas</p>
                    <p class="nx-kpi-card-value" style="color:#06B6D4">{{ $activeCount }}</p>
                    <span class="nx-kpi-card-trend">de {{ $totalCount }} cadastradas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(6,182,212,0.1);color:#06B6D4;border-color:rgba(6,182,212,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
            </div>
        </div>

        {{-- Conciliadas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Conciliadas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $reconciledCount }}</p>
                    <span class="nx-kpi-card-trend is-positive">Saldo verificado com extrato</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         BANK ACCOUNT CARDS (Glassmorphism)
    ══════════════════════════════════════════════════ --}}
    @if($accounts->where('is_active', true)->count() > 0)
    <div class="nx-bancaria-cards-section">
        <div class="nx-bancaria-cards-header">
            <h3 class="nx-bancaria-cards-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Contas Cadastradas
            </h3>
            <span class="nx-bancaria-cards-count">{{ $accounts->where('is_active', true)->count() }} conta(s)</span>
        </div>
        <div class="nx-bancaria-cards-grid">
            @foreach($accounts->where('is_active', true) as $account)
            <div class="nx-bank-card" style="--card-color: {{ $account->card_color }}">
                {{-- Card gradient background --}}
                <div class="nx-bank-card-bg"></div>
                <div class="nx-bank-card-glow"></div>

                {{-- Card Header: Logo + Name --}}
                <div class="nx-bank-card-header">
                    <div class="nx-bank-card-logo">
                        @if(str_contains(strtolower($account->bank_name ?? ''), 'nubank') || str_contains(strtolower($account->bank_name ?? ''), 'nu '))
                            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                                <circle cx="20" cy="20" r="20" fill="rgba(255,255,255,0.15)"/>
                                <text x="20" y="26" text-anchor="middle" fill="white" font-size="15" font-weight="bold" font-family="Inter,sans-serif">Nu</text>
                            </svg>
                        @elseif(str_contains(strtolower($account->bank_name ?? ''), 'itaú') || str_contains(strtolower($account->bank_name ?? ''), 'itau'))
                            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                                <circle cx="20" cy="20" r="20" fill="rgba(255,255,255,0.15)"/>
                                <text x="20" y="26" text-anchor="middle" fill="white" font-size="11" font-weight="bold" font-family="Inter,sans-serif">ITAÚ</text>
                            </svg>
                        @elseif(str_contains(strtolower($account->bank_name ?? ''), 'bradesco'))
                            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                                <circle cx="20" cy="20" r="20" fill="rgba(255,255,255,0.15)"/>
                                <text x="20" y="26" text-anchor="middle" fill="white" font-size="9" font-weight="bold" font-family="Inter,sans-serif">BRAD</text>
                            </svg>
                        @elseif($account->type === 'caixa_interno')
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                            </svg>
                        @elseif($account->type === 'digital')
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="22" x2="21" y2="22"/><rect x="5" y="12" width="2" height="10"/><rect x="11" y="8" width="2" height="14"/><rect x="17" y="4" width="2" height="18"/><polyline points="1 22 23 22"/>
                                <path d="M12 2L2 8h20L12 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="nx-bank-card-name-wrap">
                        <p class="nx-bank-card-name">{{ $account->name }}</p>
                        <p class="nx-bank-card-bank">{{ $account->bank_name }}</p>
                    </div>
                    <div class="nx-bank-card-badges">
                        <span class="nx-bank-card-type-badge">{{ $account->type_label }}</span>
                    </div>
                </div>

                {{-- Account Info --}}
                @if($account->agency || $account->number)
                <div class="nx-bank-card-info">
                    @if($account->agency)
                        <span class="nx-bank-card-info-item">
                            <span class="nx-bank-card-info-label">Agência</span>
                            <span class="nx-bank-card-info-value">{{ $account->agency }}</span>
                        </span>
                    @endif
                    @if($account->number)
                        <span class="nx-bank-card-info-sep"></span>
                        <span class="nx-bank-card-info-item">
                            <span class="nx-bank-card-info-label">Conta</span>
                            <span class="nx-bank-card-info-value">{{ $account->number }}</span>
                        </span>
                    @endif
                </div>
                @endif

                {{-- Balance --}}
                <div class="nx-bank-card-balance-section">
                    <div class="nx-bank-card-balance-item">
                        <span class="nx-bank-card-balance-label">Saldo Atual</span>
                        <span class="nx-bank-card-balance-value">{{ $account->formatted_balance }}</span>
                    </div>
                    @if($account->predicted_balance > 0)
                    <div class="nx-bank-card-balance-divider"></div>
                    <div class="nx-bank-card-balance-item">
                        <span class="nx-bank-card-balance-label">Saldo Previsto</span>
                        <span class="nx-bank-card-balance-value" style="opacity:0.75;font-size:15px;">{{ $account->formatted_predicted_balance }}</span>
                    </div>
                    @endif
                </div>

                {{-- Card Footer: Conciliation + Actions --}}
                <div class="nx-bank-card-footer">
                    <button type="button"
                        wire:click="toggleReconciled({{ $account->id }})"
                        class="nx-bank-card-reconcile {{ $account->is_reconciled ? 'is-reconciled' : 'is-pending' }}"
                        title="{{ $account->is_reconciled ? 'Conciliado — clique para desmarcar' : 'Pendente — clique para conciliar' }}">
                        @if($account->is_reconciled)
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Conciliado
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Pendente
                        @endif
                    </button>
                    <div class="nx-bank-card-actions">
                        <button type="button" wire:click="openEdit({{ $account->id }})" class="nx-bank-card-action-btn" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" wire:click="confirmDelete({{ $account->id }})" class="nx-bank-card-action-btn nx-bank-card-action-btn--danger" title="Excluir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         ACCOUNTS TABLE
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card nx-bancaria-main-card">

        {{-- ── FILTER BAR ── --}}
        <div class="nx-bancaria-filter-bar">
            <div class="nx-plano-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nx-plano-search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, banco ou número..."
                    class="nx-plano-search-input">
                @if($search)
                    <button wire:click="$set('search', '')" type="button" class="nx-plano-search-clear" title="Limpar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                @endif
            </div>

            {{-- Type filter --}}
            <div class="nx-plano-type-filter">
                <button wire:click="$set('filterType', '')" type="button"
                    class="nx-plano-type-btn {{ $filterType === '' ? 'nx-plano-type-btn--active' : '' }}">Todas</button>
                <button wire:click="$set('filterType', 'corrente')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'corrente' ? 'nx-plano-type-btn--active' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#3B82F6"></span>Corrente
                </button>
                <button wire:click="$set('filterType', 'poupanca')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'poupanca' ? 'nx-plano-type-btn--active' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#10B981"></span>Poupança
                </button>
                <button wire:click="$set('filterType', 'caixa_interno')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'caixa_interno' ? 'nx-plano-type-btn--active' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#0F766E"></span>Caixa
                </button>
                <button wire:click="$set('filterType', 'digital')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'digital' ? 'nx-plano-type-btn--active' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#7C3AED"></span>Digital
                </button>
            </div>

            {{-- Status filter --}}
            <div style="display:flex;gap:6px;margin-left:auto;">
                <button wire:click="$set('filterStatus', '')" type="button"
                    class="nx-plano-type-btn {{ $filterStatus === '' ? 'nx-plano-type-btn--active' : '' }}">Todos</button>
                <button wire:click="$set('filterStatus', 'active')" type="button"
                    class="nx-plano-type-btn {{ $filterStatus === 'active' ? 'nx-plano-type-btn--active' : '' }}">Ativos</button>
                <button wire:click="$set('filterStatus', 'inactive')" type="button"
                    class="nx-plano-type-btn {{ $filterStatus === 'inactive' ? 'nx-plano-type-btn--active' : '' }}">Inativos</button>
            </div>
        </div>

        {{-- ── TABLE HEADER ── --}}
        <div class="nx-bancaria-table-header">
            <span class="nx-tree-th" style="width:32px"></span>
            <span class="nx-tree-th" style="flex:2;min-width:160px">Conta / Banco</span>
            <span class="nx-tree-th" style="width:120px">Tipo</span>
            <span class="nx-tree-th" style="width:140px">Agência / N°</span>
            <span class="nx-tree-th" style="width:150px;text-align:right">Saldo Atual</span>
            <span class="nx-tree-th" style="width:150px;text-align:right">Saldo Previsto</span>
            <span class="nx-tree-th" style="width:110px;text-align:center">Conciliação</span>
            <span class="nx-tree-th" style="width:80px;text-align:center">Status</span>
            <span class="nx-tree-th" style="width:90px;text-align:center">Ações</span>
        </div>

        {{-- ── TABLE BODY ── --}}
        <div wire:loading.class="nx-tree-body--loading">

            @if($accounts->isEmpty())
                @include('partials.empty-state', [
                    'title'       => 'Nenhuma conta bancária encontrada',
                    'description' => 'Cadastre sua primeira conta bancária para começar a gerenciar seu fluxo financeiro.',
                ])
            @else
                @foreach($accounts as $account)
                <div class="nx-bancaria-table-row {{ !$account->is_active ? 'is-inactive' : '' }}">

                    {{-- Color indicator --}}
                    <div class="nx-bancaria-row-indicator" style="background:{{ $account->card_color }}"></div>

                    {{-- Name + Bank --}}
                    <div class="nx-bancaria-row-name" style="flex:2;min-width:160px">
                        <div class="nx-bancaria-row-avatar" style="background:{{ $account->card_color }}20;border-color:{{ $account->card_color }}40">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $account->card_color }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                @if($account->type === 'caixa_interno')
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                @elseif($account->type === 'digital')
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                                @else
                                    <line x1="3" y1="22" x2="21" y2="22"/><path d="M12 2L2 8h20L12 2z"/><rect x="6" y="12" width="4" height="10"/><rect x="14" y="12" width="4" height="10"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <p class="nx-bancaria-row-account-name">{{ $account->name }}</p>
                            <p class="nx-bancaria-row-bank-name">{{ $account->bank_name }}</p>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div style="width:120px">
                        <span class="nx-bancaria-type-chip nx-bancaria-type-{{ $account->type }}">
                            {{ $account->type_label }}
                        </span>
                    </div>

                    {{-- Agency / Number --}}
                    <div style="width:140px">
                        @if($account->agency || $account->number)
                            <p style="font-size:12.5px;color:#1E293B;font-weight:500;margin:0;">{{ $account->agency ? 'Ag: '.$account->agency : '—' }}</p>
                            <p style="font-size:12px;color:#64748B;margin:0;">{{ $account->number ? 'C/C: '.$account->number : '' }}</p>
                        @else
                            <span style="color:#CBD5E1;font-size:12px;">—</span>
                        @endif
                    </div>

                    {{-- Balance --}}
                    <div style="width:150px;text-align:right">
                        <span class="nx-bancaria-balance {{ $account->balance < 0 ? 'is-negative' : '' }}">
                            R$ {{ number_format($account->balance, 2, ',', '.') }}
                        </span>
                    </div>

                    {{-- Predicted Balance --}}
                    <div style="width:150px;text-align:right">
                        <span class="nx-bancaria-balance nx-bancaria-balance--predicted">
                            R$ {{ number_format($account->predicted_balance, 2, ',', '.') }}
                        </span>
                    </div>

                    {{-- Conciliation --}}
                    <div style="width:110px;text-align:center">
                        <button type="button"
                            wire:click="toggleReconciled({{ $account->id }})"
                            class="nx-bancaria-reconcile-badge {{ $account->is_reconciled ? 'is-reconciled' : 'is-pending' }}">
                            @if($account->is_reconciled)
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Conciliado
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Pendente
                            @endif
                        </button>
                    </div>

                    {{-- Status --}}
                    <div style="width:80px;text-align:center">
                        <button type="button"
                            wire:click="toggleActive({{ $account->id }})"
                            class="nx-status-badge {{ $account->is_active ? 'nx-status-badge--active' : 'nx-status-badge--inactive' }}">
                            {{ $account->is_active ? 'Ativo' : 'Inativo' }}
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div style="width:90px;text-align:center;display:flex;align-items:center;justify-content:center;gap:4px">
                        <button type="button" wire:click="openEdit({{ $account->id }})"
                            class="nx-icon-btn nx-icon-btn--edit" title="Editar conta">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" wire:click="confirmDelete({{ $account->id }})"
                            class="nx-icon-btn nx-icon-btn--delete" title="Excluir conta">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Table footer --}}
        @if($accounts->isNotEmpty())
        <div class="nx-bancaria-table-footer">
            <span>{{ $accounts->count() }} conta(s) exibida(s)</span>
            <div class="nx-bancaria-table-total">
                <span>Saldo Total Filtrado:</span>
                <strong>R$ {{ number_format($accounts->sum('balance'), 2, ',', '.') }}</strong>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL — CREATE / EDIT ACCOUNT
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-modal-overlay" wire:click.self="closeModal">
        <div class="nx-modal nx-modal--lg" x-data x-trap.noscroll="true">

            {{-- Header --}}
            <div class="nx-modal-header">
                <div>
                    <h2 class="nx-modal-title">
                        {{ $isEditing ? 'Editar Conta Bancária' : 'Nova Conta Bancária' }}
                    </h2>
                    <p class="nx-modal-subtitle">
                        {{ $isEditing ? 'Atualize as informações da conta.' : 'Preencha os dados para cadastrar uma nova conta.' }}
                    </p>
                </div>
                <button type="button" wire:click="closeModal" class="nx-modal-close" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="nx-modal-body">
                <form id="conta-bancaria-form" wire:submit.prevent="save">

                    {{-- Row 1: Name + Bank --}}
                    <div class="nx-field-row">
                        <div class="nx-field">
                            <label>Nome da Conta <span class="nx-required">*</span></label>
                            <input type="text" wire:model.blur="form_name" placeholder="Ex: Itaú Principal, Caixa Loja...">
                            @error('form_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Banco / Instituição <span class="nx-required">*</span></label>
                            <input type="text" wire:model.blur="form_bank_name" placeholder="Ex: Banco Itaú (Cód: 341)">
                            @error('form_bank_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Row 2: Agency + Number + Type --}}
                    <div class="nx-field-row nx-field-row--3">
                        <div class="nx-field nx-field--sm">
                            <label>Agência</label>
                            <input type="text" wire:model.blur="form_agency" placeholder="0001">
                        </div>
                        <div class="nx-field nx-field--sm">
                            <label>Número da Conta</label>
                            <input type="text" wire:model.blur="form_number" placeholder="12345-6">
                        </div>
                        <div class="nx-field">
                            <label>Tipo de Conta <span class="nx-required">*</span></label>
                            <select wire:model.live="form_type">
                                <option value="corrente">Conta Corrente</option>
                                <option value="poupanca">Poupança</option>
                                <option value="caixa_interno">Caixa Interno</option>
                                <option value="digital">Carteira Digital</option>
                            </select>
                            @error('form_type') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Row 3: Balances --}}
                    <div class="nx-field-row">
                        <div class="nx-field">
                            <label>Saldo Inicial (R$) <span class="nx-required">*</span></label>
                            <div class="nx-input-prefix-wrap">
                                <span class="nx-input-prefix">R$</span>
                                <input type="number" step="0.01" min="0" wire:model.blur="form_balance" placeholder="0,00">
                            </div>
                            @error('form_balance') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Saldo Previsto (R$)</label>
                            <div class="nx-input-prefix-wrap">
                                <span class="nx-input-prefix">R$</span>
                                <input type="number" step="0.01" min="0" wire:model.blur="form_predicted_balance" placeholder="0,00">
                            </div>
                        </div>
                    </div>

                    {{-- Row 4: Chart of Account + Color --}}
                    <div class="nx-field-row">
                        <div class="nx-field" style="flex:2">
                            <label>Conta do Plano de Contas</label>
                            <select wire:model="form_chart_of_account_id">
                                <option value="">— Não vincular —</option>
                                @foreach($chartAccounts as $chartAcc)
                                    <option value="{{ $chartAcc->id }}">{{ $chartAcc->code }} — {{ $chartAcc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field nx-field--sm">
                            <label>Cor do Card</label>
                            <div style="display:flex;align-items:center;gap:8px">
                                <input type="color" wire:model.live="form_color"
                                    style="width:42px;height:38px;border-radius:8px;border:1px solid #E2E8F0;cursor:pointer;padding:2px">
                                <span style="font-size:12px;color:#94A3B8">{{ $form_color ?: 'Auto' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Row 5: Description --}}
                    <div class="nx-field">
                        <label>Descrição / Observações</label>
                        <textarea wire:model.blur="form_description" rows="2" placeholder="Informações adicionais sobre esta conta..."></textarea>
                    </div>

                    {{-- Row 6: Toggles --}}
                    <div style="border:1px solid #F1F5F9;border-radius:10px;overflow:hidden;">
                        <label style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;cursor:pointer;background:#FAFBFD;gap:16px;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">Status da Conta</span>
                                <p class="nx-toggle-desc">
                                    {{ $form_is_active ? 'Conta ativa — disponível para lançamentos.' : 'Conta inativa — bloqueada para novos lançamentos.' }}
                                </p>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:11.5px;font-weight:700;color:{{ $form_is_active ? '#059669' : '#DC2626' }}">
                                    {{ $form_is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                                <span class="nx-switch">
                                    <input type="checkbox" wire:model.live="form_is_active">
                                    <span class="nx-switch-track"></span>
                                </span>
                            </div>
                        </label>
                        <label style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;cursor:pointer;gap:16px;border-top:1px solid #F1F5F9;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">Saldo Conciliado</span>
                                <p class="nx-toggle-desc">
                                    {{ $form_is_reconciled ? 'Saldo verificado com o extrato bancário.' : 'Saldo ainda não conciliado com o extrato.' }}
                                </p>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:11.5px;font-weight:700;color:{{ $form_is_reconciled ? '#059669' : '#F59E0B' }}">
                                    {{ $form_is_reconciled ? 'Conciliado' : 'Pendente' }}
                                </span>
                                <span class="nx-switch">
                                    <input type="checkbox" wire:model.live="form_is_reconciled">
                                    <span class="nx-switch-track"></span>
                                </span>
                            </div>
                        </label>
                    </div>

                </form>
            </div>

            {{-- Footer --}}
            <div class="nx-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="submit" form="conta-bancaria-form"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-primary">
                    <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <svg wire:loading wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="nx-spin">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Cadastrar Conta' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MODAL — TRANSFER BETWEEN ACCOUNTS
    ══════════════════════════════════════════════════ --}}
    @if($showTransferModal)
    <div class="nx-modal-overlay" wire:click.self="closeTransfer">
        <div class="nx-modal" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div>
                    <h2 class="nx-modal-title" style="color:#06B6D4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        Transferência entre Contas
                    </h2>
                    <p class="nx-modal-subtitle">Transfira saldo entre as contas cadastradas.</p>
                </div>
                <button type="button" wire:click="closeTransfer" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-field">
                    <label>Conta de Origem <span class="nx-required">*</span></label>
                    <select wire:model.live="transfer_from">
                        <option value="">— Selecione a conta de origem —</option>
                        @foreach($allAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->bank_name }}) — R$ {{ number_format($acc->balance, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                    @error('transfer_from') <span class="nx-field-error">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex;justify-content:center;margin:4px 0">
                    <div class="nx-transfer-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                    </div>
                </div>

                <div class="nx-field">
                    <label>Conta de Destino <span class="nx-required">*</span></label>
                    <select wire:model.live="transfer_to">
                        <option value="">— Selecione a conta de destino —</option>
                        @foreach($allAccounts as $acc)
                            @if($acc->id != $transfer_from)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->bank_name }})</option>
                            @endif
                        @endforeach
                    </select>
                    @error('transfer_to') <span class="nx-field-error">{{ $message }}</span> @enderror
                </div>

                <div class="nx-field">
                    <label>Valor da Transferência (R$) <span class="nx-required">*</span></label>
                    <div class="nx-input-prefix-wrap">
                        <span class="nx-input-prefix">R$</span>
                        <input type="number" step="0.01" min="0.01" wire:model.blur="transfer_amount" placeholder="0,00">
                    </div>
                    @error('transfer_amount') <span class="nx-field-error">{{ $message }}</span> @enderror
                </div>

                <div class="nx-field">
                    <label>Descrição</label>
                    <input type="text" wire:model.blur="transfer_description" placeholder="Motivo da transferência (opcional)">
                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeTransfer" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="transfer"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-transfer">
                    <svg wire:loading.remove wire:target="transfer" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    <svg wire:loading wire:target="transfer" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Transferir
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MODAL — CONFIRM DELETE
    ══════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
    <div class="nx-modal-overlay" wire:click.self="cancelDelete">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">
            <div class="nx-modal-header">
                <h2 class="nx-modal-title" style="color:#EF4444">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Excluir Conta
                </h2>
                <button type="button" wire:click="cancelDelete" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-modal-body" style="text-align:center;padding:32px 24px;">
                <div style="width:56px;height:56px;border-radius:50%;background:#FEF2F2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </div>
                <h3 style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:8px;">Confirmar exclusão</h3>
                <p style="font-size:14px;color:#64748B;line-height:1.6;">Esta ação é irreversível. A conta bancária e todos os seus dados serão permanentemente removidos.</p>
            </div>
            <div class="nx-modal-footer" style="justify-content:center;gap:12px;">
                <button type="button" wire:click="cancelDelete" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="delete" class="nx-btn nx-btn-danger"
                    wire:loading.attr="disabled" wire:loading.class="nx-btn--loading">
                    Sim, excluir conta
                </button>
            </div>
        </div>
    </div>
    @endif

</div>


