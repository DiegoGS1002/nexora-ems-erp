<div class="nx-plano-page">

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
                <span class="nx-breadcrumb-current">Plano de Contas</span>
            </nav>
            <h1 class="nx-page-title">Plano de Contas</h1>
            <p class="nx-page-subtitle">Estrutura hierárquica de categorias financeiras do sistema</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openCreate()" class="nx-btn nx-btn-primary">
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
    <div class="nx-plano-kpis">
        {{-- Total de Contas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de Contas</p>
                    <p class="nx-kpi-card-value">{{ $totalCount }}</p>
                    <span class="nx-kpi-card-trend">Cadastradas no sistema</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
            </div>
        </div>
        {{-- Contas Ativas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Contas Ativas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $activeCount }}</p>
                    <span class="nx-kpi-card-trend is-positive">Disponíveis para lançamento</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        {{-- Grupos (Sintéticas) --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Grupos (Sintéticas)</p>
                    <p class="nx-kpi-card-value" style="color:#06B6D4">{{ $groupCount }}</p>
                    <span class="nx-kpi-card-trend">Contas que possuem subcontas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(6,182,212,0.1);color:#06B6D4;border-color:rgba(6,182,212,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                </div>
            </div>
        </div>
        {{-- Contas Analíticas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Analíticas</p>
                    <p class="nx-kpi-card-value" style="color:#8B5CF6">{{ max(0, $totalCount - $groupCount) }}</p>
                    <span class="nx-kpi-card-trend">Contas lançáveis</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(139,92,246,0.1);color:#8B5CF6;border-color:rgba(139,92,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MAIN CONTENT CARD
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card nx-plano-main-card">

        {{-- ── FILTER / SEARCH BAR ── --}}
        <div class="nx-plano-filter-bar">
            <div class="nx-plano-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nx-plano-search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por código ou nome..."
                    class="nx-plano-search-input">
                @if($search)
                    <button wire:click="$set('search', '')" type="button" class="nx-plano-search-clear" title="Limpar busca">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                @endif
            </div>

            {{-- Type filter tabs --}}
            <div class="nx-plano-type-filter">
                <button wire:click="$set('filterType', '')" type="button"
                    class="nx-plano-type-btn {{ $filterType === '' ? 'nx-plano-type-btn--active' : '' }}">
                    Todos
                </button>
                <button wire:click="$set('filterType', 'receita')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'receita' ? 'nx-plano-type-btn--active nx-plano-type-btn--active-receita' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#10B981"></span>Receita
                </button>
                <button wire:click="$set('filterType', 'despesa')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'despesa' ? 'nx-plano-type-btn--active nx-plano-type-btn--active-despesa' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#EF4444"></span>Despesa
                </button>
                <button wire:click="$set('filterType', 'ativo')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'ativo' ? 'nx-plano-type-btn--active nx-plano-type-btn--active-ativo' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#3B82F6"></span>Ativo
                </button>
                <button wire:click="$set('filterType', 'passivo')" type="button"
                    class="nx-plano-type-btn {{ $filterType === 'passivo' ? 'nx-plano-type-btn--active nx-plano-type-btn--active-passivo' : '' }}">
                    <span class="nx-plano-type-dot" style="background:#F59E0B"></span>Passivo
                </button>
            </div>

            {{-- Info legend --}}
            <div class="nx-plano-legend">
                <span class="nx-plano-legend-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Sintética
                </span>
                <span class="nx-plano-legend-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Analítica
                </span>
            </div>
        </div>

        {{-- ── TREE TABLE HEADER ── --}}
        <div class="nx-tree-table-header">
            <span class="nx-tree-th nx-tree-th--icon"></span>
            <span class="nx-tree-th nx-tree-th--icon"></span>
            <span class="nx-tree-th nx-tree-th--code">Código</span>
            <span class="nx-tree-th nx-tree-th--name">Nome da Conta</span>
            <span class="nx-tree-th nx-tree-th--type">Natureza</span>
            <span class="nx-tree-th nx-tree-th--level">Tipo</span>
            <span class="nx-tree-th nx-tree-th--status">Status</span>
            <span class="nx-tree-th nx-tree-th--actions">Ações</span>
        </div>

        {{-- ── TREE BODY ── --}}
        <div class="nx-tree-body" wire:loading.class="nx-tree-body--loading">

            {{-- SEARCH RESULTS MODE --}}
            @if(trim($search) !== '')
                @if($searchResults->isEmpty())
                    @include('partials.empty-state', [
                        'title'       => 'Nenhuma conta encontrada',
                        'description' => 'Nenhuma conta corresponde à sua busca por "' . $search . '".',
                    ])
                @else
                    @foreach($searchResults as $account)
                        @php
                            $isGroup   = $account->children()->exists();
                            $typeClass = $account->type_css_class;
                        @endphp
                        <div class="nx-tree-row group {{ $isGroup ? 'nx-tree-row--group' : '' }}"
                             wire:key="search-{{ $account->id }}"
                             style="padding-left: 16px">
                            <span class="nx-tree-toggle-placeholder"></span>
                            <span class="nx-tree-icon">
                                @if($isGroup)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nx-tree-icon--folder"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nx-tree-icon--file"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                @endif
                            </span>
                            <span class="nx-tree-code">{{ $account->code }}</span>
                            <span class="nx-tree-name {{ $isGroup ? 'nx-tree-name--group' : '' }}">
                                {{ $account->name }}
                                @if($account->parent)
                                    <span class="nx-tree-parent-hint">← {{ $account->parent->code }} {{ $account->parent->name }}</span>
                                @endif
                            </span>
                            <span class="nx-tree-type-badge {{ $typeClass }}">{{ $account->type_label }}</span>
                            <span class="nx-tree-selectable-badge {{ $isGroup ? 'nx-tree-selectable--group' : ($account->is_selectable ? 'nx-tree-selectable--yes' : 'nx-tree-selectable--no') }}">
                                {{ $isGroup ? 'Sintética' : ($account->is_selectable ? 'Analítica' : 'Bloqueada') }}
                            </span>
                            <span class="nx-tree-status {{ $account->is_active ? 'nx-tree-status--active' : 'nx-tree-status--inactive' }}">
                                {{ $account->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            <div class="nx-tree-actions">
                                <button type="button" wire:click="openCreate({{ $account->id }})" class="nx-tree-action-btn nx-tree-action-btn--add" title="Adicionar Subconta">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                <button type="button" wire:click="openEdit({{ $account->id }})" class="nx-tree-action-btn nx-tree-action-btn--edit" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button type="button"
                                    wire:click="confirmDelete({{ $account->id }})"
                                    wire:confirm="Deseja excluir a conta '{{ $account->name }}'?"
                                    class="nx-tree-action-btn nx-tree-action-btn--delete" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif

            {{-- TREE MODE --}}
            @else
                @if($tree->isEmpty())
                    @include('partials.empty-state', [
                        'title'       => 'Nenhuma conta cadastrada',
                        'description' => 'Adicione a primeira conta para começar a estruturar seu plano de contas.',
                        'actionLabel' => 'Nova Conta',
                        'actionRoute' => '#',
                    ])
                @else
                    @include('livewire.financeiro.plano-contas._tree-item', [
                        'accounts' => $tree,
                        'depth'    => 0,
                    ])
                @endif
            @endif

        </div>{{-- /.nx-tree-body --}}

        {{-- ── FOOTER ── --}}
        <div class="nx-plano-footer">
            <span class="nx-plano-footer-count">
                {{ $totalCount }} {{ $totalCount === 1 ? 'conta' : 'contas' }} no total
                &nbsp;·&nbsp;
                {{ $activeCount }} {{ $activeCount === 1 ? 'ativa' : 'ativas' }}
            </span>
        </div>

    </div>{{-- /.nx-card --}}


    {{-- ══════════════════════════════════════════════════
         MODAL — CREATE / EDIT
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-modal-overlay" wire:click.self="closeModal"
         x-data x-on:keydown.escape.window="$wire.closeModal()">
        <div class="nx-modal nx-modal-lg"
             x-transition:enter="nx-modal-enter"
             x-transition:enter-start="nx-modal-enter-start"
             x-transition:enter-end="nx-modal-enter-end"
             x-transition:leave="nx-modal-leave"
             x-transition:leave-start="nx-modal-leave-start"
             x-transition:leave-end="nx-modal-leave-end">

            {{-- Header --}}
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(34,197,94,0.1);color:#22C55E;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </div>
                    <div>
                        <p class="nx-modal-title">{{ $isEditing ? 'Editar Conta' : 'Nova Conta' }}</p>
                        <p class="nx-modal-subtitle">
                            {{ $isEditing ? 'Atualize os dados da conta contábil' : 'Preencha as informações para criar a conta' }}
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="nx-modal-close" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="nx-modal-body">
                <form wire:submit="save" id="plano-contas-form">

                    {{-- Row 1: Code + Type --}}
                    <div class="grid grid-2" style="margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Código <span class="nx-required">*</span></label>
                            <input type="text"
                                wire:model.blur="form_code"
                                placeholder="Ex: 1.01.002"
                                style="font-family:'JetBrains Mono','Courier New',monospace;font-size:13px;letter-spacing:0.04em;">
                            @error('form_code') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Natureza / Tipo <span class="nx-required">*</span></label>
                            <select wire:model="form_type">
                                <option value="receita">🟢 Receita</option>
                                <option value="despesa">🔴 Despesa</option>
                                <option value="ativo">🔵 Ativo</option>
                                <option value="passivo">🟡 Passivo</option>
                            </select>
                            @error('form_type') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Row 2: Name --}}
                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Nome da Conta <span class="nx-required">*</span></label>
                        <input type="text"
                            wire:model.blur="form_name"
                            placeholder="Ex: Aluguel de Imóveis">
                        @error('form_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 3: Parent Account --}}
                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Conta Pai (opcional)</label>
                        <select wire:model="form_parent_id">
                            <option value="">— Conta raiz (sem pai) —</option>
                            @foreach($allAccounts as $acc)
                                @if(!$isEditing || $acc->id !== $editingId)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->code }} — {{ $acc->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small>Deixe vazio para criar uma conta de nível raiz.</small>
                    </div>

                    {{-- Row 4: Description --}}
                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Descrição</label>
                        <textarea wire:model.blur="form_description"
                            rows="2"
                            placeholder="Descrição opcional da conta..."></textarea>
                    </div>

                    {{-- Row 5: Toggles --}}
                    <div style="border:1px solid #F1F5F9;border-radius:10px;overflow:hidden;">
                        <label style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;cursor:pointer;background:#FAFBFD;gap:16px;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">Conta Analítica (Lançável)</span>
                                <p class="nx-toggle-desc">
                                    {{ $form_is_selectable ? 'Esta conta pode receber lançamentos financeiros diretos.' : 'Esta conta não aceita lançamentos — apenas agrupa subcontas.' }}
                                </p>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:11.5px;font-weight:700;color:{{ $form_is_selectable ? '#059669' : '#94A3B8' }}">
                                    {{ $form_is_selectable ? 'Sim' : 'Não' }}
                                </span>
                                <span class="nx-switch">
                                    <input type="checkbox" wire:model.live="form_is_selectable">
                                    <span class="nx-switch-track"></span>
                                </span>
                            </div>
                        </label>
                        <label style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;cursor:pointer;gap:16px;border-top:1px solid #F1F5F9;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">Status da Conta</span>
                                <p class="nx-toggle-desc">
                                    {{ $form_is_active ? 'Conta ativa — disponível para novos lançamentos.' : 'Conta inativa — bloqueada para novos lançamentos.' }}
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
                    </div>

                </form>
            </div>

            {{-- Footer --}}
            <div class="nx-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="submit" form="plano-contas-form"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-primary">
                    <svg wire:loading.remove wire:target="save"
                         xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <svg wire:loading wire:target="save" class="nx-spin"
                         xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Criar Conta' }}
                </button>
            </div>

        </div>
    </div>
    @endif

</div>

