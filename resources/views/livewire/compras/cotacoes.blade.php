<div class="nx-so-page">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'compras') }}" class="nx-breadcrumb-link" wire:navigate>Compras</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Cotações de Compra</span>
            </nav>
            <h1 class="nx-page-title">Cotações de Compra</h1>
            <p class="nx-page-subtitle">Comparação de preços e seleção de fornecedores</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Cotação
            </button>
        </div>
    </div>

    {{-- ── FLASH ────────────────────────────────────── --}}
    @session('message')
        <div class="nx-op-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="nx-op-alert-success" style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.2);color:#DC2626;" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div class="nx-op-kpis" style="grid-template-columns:repeat(5,minmax(0,1fr))">
        <div class="nx-kpi-card" style="border-top:3px solid #6366F1">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de Cotações</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #3B82F6">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Abertas</p>
                    <p class="nx-kpi-card-value" style="color:#3B82F6">{{ $stats['abertas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.08);color:#3B82F6;border-color:rgba(59,130,246,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #F59E0B">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Aguardando</p>
                    <p class="nx-kpi-card-value" style="color:#F59E0B">{{ $stats['aguardando'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(245,158,11,0.08);color:#F59E0B;border-color:rgba(245,158,11,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #10B981">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Aprovadas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['aprovadas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #059669">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Convertidas</p>
                    <p class="nx-kpi-card-value" style="color:#059669">{{ $stats['convertidas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(5,150,105,0.08);color:#059669;border-color:rgba(5,150,105,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTERS ──────────────────────────────────── --}}
    <div class="nx-op-filters">
        <div class="nx-search-wrap" style="max-width:320px;flex:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar n° cotação ou título..." class="nx-search">
        </div>
        <select wire:model.live="filterStatus" class="nx-filter-select">
            <option value="">Todos os Status</option>
            @foreach($statuses as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
        @if($search || $filterStatus)
            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm" style="color:#EF4444;border-color:rgba(239,68,68,0.3)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Limpar
            </button>
        @endif
    </div>

    {{-- ── TABLE ────────────────────────────────────── --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Cotação</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th class="nx-th-right">Itens</th>
                        <th class="nx-th-right">Fornecedores</th>
                        <th>Prazo Resp.</th>
                        <th>Criado em</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cotacoes as $cotacao)
                        <tr wire:key="cot-{{ $cotacao->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;">{{ $cotacao->number }}</strong>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $cotacao->title }}</div>
                            </td>
                            <td>
                                <span class="nx-so-badge {{ $cotacao->status->badgeClass() }}">
                                    {{ $cotacao->status->label() }}
                                </span>
                            </td>
                            <td class="nx-td-right" style="font-size:13px;color:#475569;">
                                {{ $cotacao->items_count ?? 0 }}
                            </td>
                            <td class="nx-td-right" style="font-size:13px;color:#475569;">
                                @php
                                    $supplierCount = \App\Models\CotacaoResposta::where('cotacao_id', $cotacao->id)
                                        ->distinct('supplier_id')->count('supplier_id');
                                @endphp
                                {{ $supplierCount }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                @if($cotacao->deadline_date)
                                    <span style="{{ $cotacao->deadline_date->isPast() && !in_array($cotacao->status->value, ['aprovada','convertida','cancelada']) ? 'color:#EF4444;font-weight:600;' : '' }}">
                                        {{ $cotacao->deadline_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size:12px;color:#64748B;">{{ $cotacao->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" wire:click="openDetail({{ $cotacao->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Ver detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $cotacao->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                @include('partials.empty-state', [
                                    'title'       => 'Nenhuma cotação encontrada',
                                    'description' => 'Crie uma nova cotação clicando no botão acima.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cotacoes->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $cotacoes->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — CREATE / EDIT
    ══════════════════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeModal">
        <div class="nx-so-modal" style="max-width:900px;">

            {{-- Modal Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 0;flex-shrink:0;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#0F172A;margin:0;">
                        {{ $editingId ? 'Editar Cotação' : 'Nova Cotação de Compra' }}
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                        Defina os itens, adicione fornecedores e compare preços
                    </p>
                </div>
                <button type="button" wire:click="closeModal"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Tab Navigation --}}
            <div class="nx-so-tabs">
                @php
                    $modalTabs = [
                        ['key'=>'geral',        'label'=>'Geral',         'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                        ['key'=>'itens',        'label'=>'Itens',         'icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
                        ['key'=>'fornecedores', 'label'=>'Fornecedores',  'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                        ['key'=>'comparativo',  'label'=>'Comparativo',   'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                        ['key'=>'obs',          'label'=>'Observações',   'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                    ];
                @endphp
                @foreach($modalTabs as $tab)
                    <button type="button" wire:click="setTab('{{ $tab['key'] }}')"
                        class="nx-so-tab {{ $activeTab === $tab['key'] ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $tab['icon'] !!}</svg>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Modal Body --}}
            <form wire:submit="save" class="nx-so-modal-body">

                @if($errors->any())
                    <div class="alert-error" style="margin-bottom:16px;">
                        <strong>Corrija os erros:</strong>
                        <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                {{-- ── ABA: GERAL ──────────────────────────────────── --}}
                @if($activeTab === 'geral')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Identificação da Cotação
                    </h3>
                    <div class="nx-field">
                        <label>Título / Descrição <span style="color:#EF4444">*</span></label>
                        <input type="text" wire:model="title" placeholder="Ex: Cotação de materiais de escritório — Abril/2026">
                        @error('title')<span class="nx-field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="nx-so-grid-3">
                        <div class="nx-field">
                            <label>Status</label>
                            <select wire:model="status">
                                @foreach($statuses as $s)
                                    <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Prazo para Resposta</label>
                            <input type="date" wire:model="deadline_date">
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── ABA: ITENS ───────────────────────────────────── --}}
                @if($activeTab === 'itens')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        Itens a Cotar
                    </h3>

                    <div style="position:relative;margin-bottom:16px;">
                        <div style="display:flex;gap:8px;">
                            <div class="nx-search-wrap" style="flex:1;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" wire:model.live.debounce.300ms="searchProduct"
                                    placeholder="Buscar produto por nome, código ou EAN..." class="nx-search">
                            </div>
                            <button type="button" wire:click="addManualItem"
                                class="nx-btn nx-btn-outline nx-btn-sm" style="white-space:nowrap;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Item Manual
                            </button>
                        </div>
                        @if(count($searchResults))
                            <div class="nx-so-search-results">
                                @foreach($searchResults as $prod)
                                    <div class="nx-so-search-result-item" wire:click="addProduct('{{ $prod['id'] }}')">
                                        <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $prod['name'] }}</div>
                                        <div style="font-size:11px;color:#94A3B8;">
                                            @if($prod['product_code']) Cód: {{ $prod['product_code'] }} @endif
                                            @if($prod['ean']) · EAN: {{ $prod['ean'] }} @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('cotacaoItems')<div class="alert-error" style="margin-bottom:12px;">{{ $message }}</div>@enderror

                    @forelse($cotacaoItems as $i => $item)
                        <div class="nx-so-item-card" wire:key="cot-item-{{ $i }}">
                            <div class="nx-so-item-header">
                                <div class="nx-so-item-num">{{ $i + 1 }}</div>
                                <div style="flex:1;font-size:12px;color:#94A3B8;font-family:monospace;">
                                    @if($item['sku']) {{ $item['sku'] }} @endif
                                </div>
                                <button type="button" wire:click="removeItem({{ $i }})"
                                    style="width:26px;height:26px;border-radius:6px;border:none;background:#FEE2E2;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#DC2626;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div class="nx-so-item-fields" style="grid-template-columns:2fr 1fr 1fr 1fr;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Descrição</label>
                                    <input type="text" wire:model.blur="cotacaoItems.{{ $i }}.description" placeholder="Descrição do item">
                                    @error("cotacaoItems.$i.description")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Unidade</label>
                                    <input type="text" wire:model="cotacaoItems.{{ $i }}.unit" placeholder="UN">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Quantidade</label>
                                    <input type="number" step="0.001" min="0.001" wire:model.blur="cotacaoItems.{{ $i }}.quantity">
                                    @error("cotacaoItems.$i.quantity")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>SKU / Código</label>
                                    <input type="text" wire:model="cotacaoItems.{{ $i }}.sku" placeholder="Opcional">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="nx-so-empty-items">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            <p>Nenhum item adicionado</p>
                            <span>Busque um produto ou adicione um item manual</span>
                        </div>
                    @endforelse

                    @if(count($cotacaoItems))
                        <div class="nx-so-items-summary">
                            <span>{{ count($cotacaoItems) }} {{ count($cotacaoItems) === 1 ? 'item' : 'itens' }}</span>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ── ABA: FORNECEDORES ────────────────────────────── --}}
                @if($activeTab === 'fornecedores')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Fornecedores e Preços
                    </h3>

                    @if(count($cotacaoItems) === 0)
                        <div style="padding:20px;text-align:center;background:#FFF7ED;border-radius:10px;border:1px solid #FED7AA;">
                            <p style="font-size:13px;color:#92400E;margin:0;">⚠️ Adicione itens na aba <strong>Itens</strong> antes de incluir fornecedores.</p>
                        </div>
                    @else
                        {{-- Supplier search --}}
                        <div style="position:relative;margin-bottom:20px;">
                            <div class="nx-search-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" wire:model.live.debounce.300ms="searchSupplierTerm"
                                    placeholder="Buscar fornecedor por nome ou CNPJ..." class="nx-search">
                            </div>
                            @if(count($supplierSearchResults))
                                <div class="nx-so-search-results">
                                    @foreach($supplierSearchResults as $sup)
                                        <div class="nx-so-search-result-item" wire:click="addSupplier('{{ $sup['id'] }}')">
                                            <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $sup['social_name'] ?? $sup['name'] }}</div>
                                            @if($sup['taxNumber'])
                                                <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $sup['taxNumber'] }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Supplier Cards with price inputs --}}
                        @forelse($cotacaoSuppliers as $si => $supplierRow)
                            <div style="border:1px solid #E2E8F0;border-radius:12px;padding:16px;margin-bottom:14px;background:#FAFAFA;" wire:key="sup-{{ $si }}">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:8px;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;color:#6366F1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <div>
                                            <div style="font-size:13px;font-weight:700;color:#0F172A;">{{ $supplierRow['supplier_name'] }}</div>
                                            <div style="font-size:11px;color:#94A3B8;">Fornecedor {{ $si + 1 }}</div>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeSupplier({{ $si }})"
                                        style="width:28px;height:28px;border-radius:6px;border:none;background:#FEE2E2;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#DC2626;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>

                                {{-- Info fields --}}
                                <div style="display:grid;grid-template-columns:1fr 2fr;gap:10px;margin-bottom:14px;">
                                    <div class="nx-field" style="margin-bottom:0;">
                                        <label>Prazo de Entrega (dias)</label>
                                        <input type="number" min="0" wire:model="cotacaoSuppliers.{{ $si }}.delivery_days" placeholder="Ex: 15">
                                    </div>
                                    <div class="nx-field" style="margin-bottom:0;">
                                        <label>Observação do Fornecedor</label>
                                        <input type="text" wire:model="cotacaoSuppliers.{{ $si }}.notes" placeholder="Condições, prazos, etc.">
                                    </div>
                                </div>

                                {{-- Prices per item --}}
                                <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">
                                    Preços por Item
                                </div>
                                @foreach($cotacaoItems as $ii => $item)
                                    <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid #F1F5F9;" wire:key="sup-{{ $si }}-item-{{ $ii }}">
                                        <div style="flex:1;font-size:12px;color:#1E293B;">
                                            <span style="font-size:11px;background:#F1F5F9;border-radius:4px;padding:1px 6px;color:#64748B;margin-right:6px;">{{ $ii + 1 }}</span>
                                            {{ $item['description'] ?: '(sem descrição)' }}
                                            <span style="font-size:11px;color:#94A3B8;margin-left:6px;">{{ $item['quantity'] }} {{ $item['unit'] }}</span>
                                        </div>
                                        <div style="width:140px;">
                                            <div class="nx-field" style="margin-bottom:0;">
                                                <label style="font-size:10px;">Preço Unitário (R$)</label>
                                                <input type="number" step="0.01" min="0"
                                                    wire:model="cotacaoSuppliers.{{ $si }}.prices.{{ $ii }}"
                                                    placeholder="0,00"
                                                    style="text-align:right;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="nx-so-empty-items">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <p>Nenhum fornecedor adicionado</p>
                                <span>Busque e adicione fornecedores para registrar os preços</span>
                            </div>
                        @endforelse
                    @endif
                </div>
                @endif

                {{-- ── ABA: COMPARATIVO ────────────────────────────── --}}
                @if($activeTab === 'comparativo')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Comparativo de Preços
                    </h3>

                    @if(count($cotacaoItems) === 0 || count($cotacaoSuppliers) === 0)
                        <div style="padding:24px;text-align:center;background:#F8FAFC;border-radius:10px;border:1px dashed #CBD5E1;">
                            <p style="font-size:13px;color:#94A3B8;margin:0;">Adicione itens e fornecedores para visualizar o comparativo.</p>
                        </div>
                    @else
                        <div class="nx-table-wrap">
                            <table class="nx-table" style="font-size:12px;">
                                <thead>
                                    <tr>
                                        <th style="min-width:160px;">Item</th>
                                        <th class="nx-th-right" style="min-width:80px;">Qtd</th>
                                        @foreach($cotacaoSuppliers as $si => $sup)
                                            <th class="nx-th-right" style="min-width:120px;">{{ $sup['supplier_name'] }}</th>
                                        @endforeach
                                        <th class="nx-th-right" style="min-width:110px;color:#10B981;">Melhor Preço</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cotacaoItems as $ii => $item)
                                        @php
                                            $prices = collect($cotacaoSuppliers)->map(fn($s) => (float)($s['prices'][$ii] ?? 0));
                                            $validPrices = $prices->filter(fn($p) => $p > 0);
                                            $minPrice = $validPrices->min();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div style="font-weight:600;color:#1E293B;">{{ $item['description'] ?: '—' }}</div>
                                                @if($item['sku'])<div style="font-size:10px;color:#94A3B8;font-family:monospace;">{{ $item['sku'] }}</div>@endif
                                            </td>
                                            <td class="nx-td-right" style="color:#64748B;">{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                                            @foreach($cotacaoSuppliers as $si => $sup)
                                                @php $price = (float)($sup['prices'][$ii] ?? 0); @endphp
                                                <td class="nx-td-right"
                                                    style="{{ $price > 0 && $price == $minPrice ? 'color:#059669;font-weight:700;background:#F0FDF4;' : '' }}">
                                                    @if($price > 0)
                                                        R$ {{ number_format($price, 2, ',', '.') }}
                                                        @if($price == $minPrice)
                                                            <span style="font-size:10px;margin-left:4px;">✓</span>
                                                        @endif
                                                    @else
                                                        <span style="color:#CBD5E1;">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="nx-td-right" style="font-weight:700;color:#059669;">
                                                @if($minPrice)
                                                    R$ {{ number_format($minPrice, 2, ',', '.') }}
                                                @else
                                                    <span style="color:#CBD5E1;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background:#F8FAFC;">
                                        <td colspan="2" style="font-size:12px;font-weight:700;color:#475569;padding:10px 12px;">
                                            Total Estimado
                                        </td>
                                        @foreach($cotacaoSuppliers as $si => $sup)
                                            @php
                                                $total = collect($cotacaoItems)->sum(function($item, $ii) use ($sup) {
                                                    $price = (float)($sup['prices'][$ii] ?? 0);
                                                    $qty   = (float)($item['quantity'] ?? 1);
                                                    return $price * $qty;
                                                });
                                            @endphp
                                            <td class="nx-td-right" style="font-size:13px;font-weight:700;color:#0F172A;padding:10px 12px;">
                                                R$ {{ number_format($total, 2, ',', '.') }}
                                            </td>
                                        @endforeach
                                        <td class="nx-td-right" style="font-size:13px;font-weight:700;color:#059669;padding:10px 12px;">
                                            @php
                                                $bestTotal = collect($cotacaoItems)->sum(function($item, $ii) {
                                                    $prices = collect($this->cotacaoSuppliers)->map(fn($s) => (float)($s['prices'][$ii] ?? 0))->filter(fn($p) => $p > 0);
                                                    $minP = $prices->min() ?? 0;
                                                    return $minP * (float)($item['quantity'] ?? 1);
                                                });
                                            @endphp
                                            R$ {{ number_format($bestTotal, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div style="margin-top:12px;padding:10px 14px;background:#F0FDF4;border-radius:8px;border:1px solid #BBF7D0;">
                            <p style="font-size:12px;color:#166534;margin:0;">
                                ✓ Os preços marcados em <strong>verde</strong> representam o menor preço para cada item. No detalhe da cotação você poderá selecionar o fornecedor vencedor por item.
                            </p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ── ABA: OBSERVAÇÕES ─────────────────────────────── --}}
                @if($activeTab === 'obs')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Observações
                    </h3>
                    <div class="nx-field">
                        <label>Observações Internas</label>
                        <textarea wire:model="notes" rows="6" placeholder="Informações adicionais sobre a cotação, critérios de seleção, etc."></textarea>
                    </div>
                </div>
                @endif

            </form>

            {{-- Modal Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">
                    Cancelar
                </button>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:12px;color:#94A3B8;">
                        {{ count($cotacaoItems) }} {{ count($cotacaoItems) === 1 ? 'item' : 'itens' }}
                        · {{ count($cotacaoSuppliers) }} {{ count($cotacaoSuppliers) === 1 ? 'fornecedor' : 'fornecedores' }}
                    </span>
                    <button type="button" wire:click="save" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ $editingId ? 'Salvar Alterações' : 'Criar Cotação' }}
                        </span>
                        <span wire:loading wire:target="save">Salvando...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — DETALHE
    ══════════════════════════════════════════════════════════════ --}}
    @if($showDetail && $viewingCotacao)
    <div class="nx-so-modal-wrap" wire:click.self="closeDetail">
        <div class="nx-so-modal" style="max-width:860px;">

            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                        <strong style="font-family:monospace;font-size:16px;color:#6366F1;">{{ $viewingCotacao->number }}</strong>
                        <span class="nx-so-badge {{ $viewingCotacao->status->badgeClass() }}">{{ $viewingCotacao->status->label() }}</span>
                    </div>
                    <p style="font-size:13px;color:#64748B;margin:0;">{{ $viewingCotacao->title }}</p>
                </div>
                <button type="button" wire:click="closeDetail"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-so-modal-body">

                {{-- Info grid --}}
                <div class="nx-so-detail-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Prazo para Resposta</span>
                        <strong>{{ $viewingCotacao->deadline_date?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Criado por</span>
                        <strong>{{ $viewingCotacao->creator?->name ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Data de Criação</span>
                        <strong>{{ $viewingCotacao->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if($viewingCotacao->purchaseOrder)
                    <div class="nx-so-detail-item" style="grid-column:1/-1;">
                        <span class="nx-so-detail-label">Pedido de Compra Gerado</span>
                        <strong style="color:#6366F1;font-family:monospace;">{{ $viewingCotacao->purchaseOrder->order_number }}</strong>
                    </div>
                    @endif
                </div>

                {{-- Comparison table --}}
                @php
                    $allSupplierIds = $viewingCotacao->respostas->pluck('supplier_id')->unique()->values();
                    $suppliersMap   = \App\Models\Supplier::whereIn('id', $allSupplierIds)->get()->keyBy('id');
                @endphp

                @if($viewingCotacao->items->count() > 0 && $allSupplierIds->count() > 0)
                <h4 style="font-size:13px;font-weight:700;color:#0F172A;margin-bottom:10px;">Comparativo de Preços</h4>
                <div class="nx-table-wrap" style="margin-bottom:20px;">
                    <table class="nx-table" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="nx-th-right">Qtd</th>
                                @foreach($allSupplierIds as $sid)
                                    <th class="nx-th-right">{{ $suppliersMap[$sid]?->social_name ?? $suppliersMap[$sid]?->name ?? $sid }}</th>
                                @endforeach
                                <th class="nx-th-right" style="color:#10B981;">Melhor</th>
                                <th style="width:80px;text-align:center;">Seleção</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingCotacao->items as $item)
                                @php
                                    $itemRespostas = $item->respostas->keyBy('supplier_id');
                                    $validPrices   = $item->respostas->where('unit_price', '>', 0)->pluck('unit_price');
                                    $minPrice      = $validPrices->min();
                                    $selectedResp  = $item->respostas->firstWhere('selected', true);
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight:600;color:#1E293B;">{{ $item->description }}</div>
                                        @if($item->sku)<div style="font-size:10px;color:#94A3B8;font-family:monospace;">{{ $item->sku }}</div>@endif
                                    </td>
                                    <td class="nx-td-right" style="color:#64748B;">{{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }}</td>
                                    @foreach($allSupplierIds as $sid)
                                        @php
                                            $resposta = $itemRespostas[$sid] ?? null;
                                            $price    = $resposta ? (float) $resposta->unit_price : 0;
                                            $isBest   = $price > 0 && $price == $minPrice;
                                            $isSelected = $resposta && $resposta->selected;
                                        @endphp
                                        <td class="nx-td-right" style="{{ $isBest ? 'color:#059669;font-weight:700;background:#F0FDF4;' : '' }}{{ $isSelected ? 'border-left:2px solid #10B981;' : '' }}">
                                            @if($price > 0)
                                                R$ {{ number_format($price, 2, ',', '.') }}
                                                @if($isBest)<span style="font-size:10px;">✓</span>@endif
                                                @if($isSelected) <span style="font-size:10px;background:#DCFCE7;color:#15803D;border-radius:3px;padding:1px 4px;">sel.</span>@endif
                                            @else
                                                <span style="color:#CBD5E1;">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="nx-td-right" style="font-weight:700;color:#059669;">
                                        @if($minPrice) R$ {{ number_format($minPrice, 2, ',', '.') }}
                                        @else <span style="color:#CBD5E1;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($selectedResp)
                                            <button type="button"
                                                wire:click="selectResposta({{ $item->id }}, '{{ $selectedResp->supplier_id }}')"
                                                style="font-size:10px;padding:3px 8px;border-radius:4px;border:none;background:#DCFCE7;color:#15803D;cursor:pointer;">
                                                Selecionado
                                            </button>
                                        @elseif($minPrice)
                                            @php $bestSupId = $item->respostas->where('unit_price', $minPrice)->first()?->supplier_id; @endphp
                                            @if($bestSupId)
                                                <button type="button"
                                                    wire:click="selectResposta({{ $item->id }}, '{{ $bestSupId }}')"
                                                    style="font-size:10px;padding:3px 8px;border-radius:4px;border:none;background:#F1F5F9;color:#475569;cursor:pointer;">
                                                    Selecionar
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- Notes --}}
                @if($viewingCotacao->notes)
                    <div style="margin-top:14px;padding:12px 14px;background:#F8FAFC;border-radius:8px;border-left:3px solid #CBD5E1;">
                        <strong style="font-size:12px;color:#64748B;">Observações:</strong>
                        <p style="font-size:13px;color:#475569;margin:4px 0 0;">{{ $viewingCotacao->notes }}</p>
                    </div>
                @endif

            </div>

            {{-- Detail Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-ghost">Fechar</button>
                <div style="display:flex;gap:8px;">
                    @if($viewingCotacao->status === \App\Enums\CotacaoStatus::Respondida)
                        <button type="button" wire:click="approveCotacao({{ $viewingCotacao->id }})"
                            class="nx-btn" style="background:#10B981;color:#fff;border-color:#10B981;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Aprovar Cotação
                        </button>
                    @endif
                    @if($viewingCotacao->status === \App\Enums\CotacaoStatus::Aprovada)
                        <button type="button"
                            wire:click="convertToPurchaseOrder({{ $viewingCotacao->id }})"
                            wire:confirm="Deseja gerar um Pedido de Compra com os itens selecionados?"
                            class="nx-btn nx-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            Gerar Pedido de Compra
                        </button>
                    @endif
                    @if(!in_array($viewingCotacao->status, [\App\Enums\CotacaoStatus::Cancelada, \App\Enums\CotacaoStatus::Convertida]))
                        <button type="button" wire:click="edit({{ $viewingCotacao->id }})" class="nx-btn nx-btn-outline">
                            Editar
                        </button>
                        <button type="button"
                            wire:click="cancelCotacao({{ $viewingCotacao->id }})"
                            wire:confirm="Tem certeza que deseja cancelar esta cotação?"
                            class="nx-btn" style="background:#FEF2F2;color:#DC2626;border-color:rgba(239,68,68,0.3);">
                            Cancelar
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

</div>

