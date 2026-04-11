<div class="nx-so-page">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'compras') }}" class="nx-breadcrumb-link" wire:navigate>Compras</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Pedidos de Compra</span>
            </nav>
            <h1 class="nx-page-title">Pedidos de Compra</h1>
            <p class="nx-page-subtitle">Gestão completa de ordens de compra e fornecedores</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Pedido
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
                    <p class="nx-kpi-card-title">Total de Pedidos</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #F59E0B">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Aguard. Aprovação</p>
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
                    <p class="nx-kpi-card-title">Aprovados</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['aprovado'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #059669">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Recebidos</p>
                    <p class="nx-kpi-card-value" style="color:#059669">{{ $stats['recebido'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(5,150,105,0.08);color:#059669;border-color:rgba(5,150,105,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #0EA5E9">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Valor Total</p>
                    <p class="nx-kpi-card-value" style="color:#0EA5E9;font-size:16px">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(14,165,233,0.08);color:#0EA5E9;border-color:rgba(14,165,233,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTERS ──────────────────────────────────── --}}
    <div class="nx-op-filters">
        <div class="nx-search-wrap" style="max-width:320px;flex:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar n° pedido ou fornecedor..." class="nx-search">
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
                        <th>Pedido</th>
                        <th>Fornecedor</th>
                        <th>Comprador</th>
                        <th>Origem</th>
                        <th>Status</th>
                        <th class="nx-th-right">Itens</th>
                        <th class="nx-th-right">Total</th>
                        <th>Data</th>
                        <th>Previsão</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr wire:key="pc-{{ $order->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;">{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:600;color:#1E293B;">
                                    {{ $order->supplier?->social_name ?? $order->supplier?->name ?? 'N/A' }}
                                </div>
                                @if($order->supplier?->taxNumber)
                                    <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $order->supplier->taxNumber }}</div>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#475569;">{{ $order->buyer?->name ?? '—' }}</td>
                            <td>
                                <span style="font-size:11px;font-weight:600;padding:3px 7px;border-radius:5px;background:#F1F5F9;color:#475569;">
                                    {{ $order->origin?->label() ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="nx-so-badge {{ $order->status->badgeClass() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="nx-td-right" style="font-size:13px;color:#475569;">{{ $order->items_count ?? $order->items->count() }}</td>
                            <td class="nx-td-right" style="font-size:13px;font-weight:600;color:#0F172A;">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                {{ $order->order_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                {{ $order->expected_delivery_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" wire:click="openDetail({{ $order->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Ver detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $order->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                @include('partials.empty-state', [
                                    'title'       => 'Nenhum pedido de compra encontrado',
                                    'description' => 'Crie um novo pedido clicando no botão acima.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — CREATE / EDIT
    ══════════════════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeModal">
        <div class="nx-so-modal">

            {{-- Modal Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 0;flex-shrink:0;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#0F172A;margin:0;">
                        {{ $editingId ? 'Editar Pedido de Compra' : 'Novo Pedido de Compra' }}
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                        Preencha os dados do pedido e adicione os itens
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
                        ['key'=>'geral',     'label'=>'Geral',      'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                        ['key'=>'itens',     'label'=>'Itens',      'icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
                        ['key'=>'pagamento', 'label'=>'Pagamento',  'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                        ['key'=>'totais',    'label'=>'Totais',     'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                        ['key'=>'logistica', 'label'=>'Logística',  'icon'=>'<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
                        ['key'=>'obs',       'label'=>'Observações','icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
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

                {{-- Errors --}}
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Fornecedor & Identificação
                    </h3>
                    <div class="nx-so-grid-2">
                        <div class="nx-field">
                            <label>Fornecedor <span style="color:#EF4444">*</span></label>
                            <select wire:model="supplier_id">
                                <option value="">— Selecione o fornecedor —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->social_name ?? $s->name }} @if($s->taxNumber) — {{ $s->taxNumber }} @endif</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<span class="nx-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nx-field">
                            <label>Comprador Responsável</label>
                            <select wire:model="buyer_id">
                                <option value="">— Selecione —</option>
                                @foreach($buyers as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="nx-so-grid-3">
                        <div class="nx-field">
                            <label>Status</label>
                            <select wire:model="status">
                                @foreach($statuses as $s)
                                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Origem</label>
                            <select wire:model="origin">
                                @foreach($origens as $o)
                                    <option value="{{ $o->value }}">{{ $o->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Data do Pedido <span style="color:#EF4444">*</span></label>
                            <input type="datetime-local" wire:model="order_date">
                            @error('order_date')<span class="nx-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="nx-so-grid-2">
                        <div class="nx-field">
                            <label>Previsão de Entrega</label>
                            <input type="date" wire:model="expected_delivery_date">
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── ABA: ITENS ───────────────────────────────────── --}}
                @if($activeTab === 'itens')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        Itens do Pedido
                    </h3>

                    {{-- Busca de produto --}}
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
                                            · Custo: R$ {{ number_format($prod['cost_price'] ?? 0, 2, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('orderItems')<div class="alert-error" style="margin-bottom:12px;">{{ $message }}</div>@enderror

                    @forelse($orderItems as $i => $item)
                        <div class="nx-so-item-card" wire:key="item-{{ $i }}">
                            <div class="nx-so-item-header">
                                <div class="nx-so-item-num">{{ $i + 1 }}</div>
                                <div style="flex:1;font-size:13px;font-weight:600;color:#1E293B;">
                                    @if($item['sku']) <span style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $item['sku'] }}</span> @endif
                                </div>
                                <button type="button" wire:click="removeItem({{ $i }})"
                                    style="width:26px;height:26px;border-radius:6px;border:none;background:#FEE2E2;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#DC2626;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div class="nx-so-item-fields" style="grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Descrição</label>
                                    <input type="text" wire:model.blur="orderItems.{{ $i }}.description" placeholder="Descrição do item">
                                    @error("orderItems.$i.description")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Unidade</label>
                                    <input type="text" wire:model="orderItems.{{ $i }}.unit" placeholder="UN">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Qtd</label>
                                    <input type="number" step="0.001" min="0" wire:model.blur="orderItems.{{ $i }}.quantity">
                                    @error("orderItems.$i.quantity")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Preço Unit.</label>
                                    <input type="number" step="0.01" min="0" wire:model.blur="orderItems.{{ $i }}.unit_price">
                                    @error("orderItems.$i.unit_price")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Desconto R$</label>
                                    <input type="number" step="0.01" min="0" wire:model.blur="orderItems.{{ $i }}.discount">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Total</label>
                                    <div class="nx-so-item-total">
                                        R$ {{ number_format(max(0, ((float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0)) - (float)($item['discount'] ?? 0)), 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top:8px;">
                                <div class="nx-field" style="margin-bottom:0;max-width:260px;">
                                    <label>Centro de Custo</label>
                                    <input type="text" wire:model="orderItems.{{ $i }}.cost_center" placeholder="Ex: ADM-001">
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

                    @if(count($orderItems))
                        <div class="nx-so-items-summary">
                            <span>{{ count($orderItems) }} {{ count($orderItems) === 1 ? 'item' : 'itens' }}</span>
                            <span style="font-weight:700;color:#0F172A;">Subtotal: R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ── ABA: PAGAMENTO ───────────────────────────────── --}}
                @if($activeTab === 'pagamento')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Condições de Pagamento
                    </h3>
                    <div class="nx-so-grid-2">
                        <div class="nx-field">
                            <label>Condição de Pagamento</label>
                            <select wire:model="payment_condition">
                                <option value="">— Selecione —</option>
                                <option value="a_vista">À Vista</option>
                                <option value="7_dias">7 Dias</option>
                                <option value="15_dias">15 Dias</option>
                                <option value="30_dias">30 Dias</option>
                                <option value="30_60">30/60 Dias</option>
                                <option value="30_60_90">30/60/90 Dias</option>
                                <option value="45_dias">45 Dias</option>
                                <option value="60_dias">60 Dias</option>
                                <option value="90_dias">90 Dias</option>
                                <option value="personalizado">Personalizado</option>
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Forma de Pagamento</label>
                            <select wire:model="payment_method">
                                <option value="">— Selecione —</option>
                                <option value="boleto">Boleto Bancário</option>
                                <option value="pix">PIX</option>
                                <option value="transferencia">Transferência Bancária</option>
                                <option value="cheque">Cheque</option>
                                <option value="cartao">Cartão</option>
                                <option value="dinheiro">Dinheiro</option>
                            </select>
                        </div>
                    </div>
                    <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:14px 16px;margin-top:8px;">
                        <div style="font-size:12px;font-weight:600;color:#15803D;margin-bottom:4px;">💡 Dica</div>
                        <p style="font-size:12px;color:#166534;margin:0;">
                            As condições de pagamento definidas aqui serão usadas como referência para geração de contas a pagar quando o pedido for recebido.
                        </p>
                    </div>
                </div>
                @endif

                {{-- ── ABA: TOTAIS ──────────────────────────────────── --}}
                @if($activeTab === 'totais')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Resumo Financeiro
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Desconto (R$)</label>
                            <input type="number" step="0.01" min="0" wire:model.blur="discount_amount" placeholder="0,00">
                        </div>
                        <div class="nx-field">
                            <label>Frete (R$)</label>
                            <input type="number" step="0.01" min="0" wire:model.blur="shipping_amount" placeholder="0,00">
                        </div>
                        <div class="nx-field">
                            <label>Outras Despesas (R$)</label>
                            <input type="number" step="0.01" min="0" wire:model.blur="other_expenses" placeholder="0,00">
                        </div>
                    </div>

                    <div class="nx-so-totals-box">
                        <div class="nx-so-totals-row">
                            <span>Subtotal dos Itens</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-so-totals-row">
                            <span>(-) Desconto</span>
                            <span style="color:#EF4444;">- R$ {{ number_format($discount_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-so-totals-row">
                            <span>(+) Frete</span>
                            <span>R$ {{ number_format($shipping_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-so-totals-row">
                            <span>(+) Outras Despesas</span>
                            <span>R$ {{ number_format($other_expenses, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-so-totals-divider"></div>
                        <div class="nx-so-totals-final">
                            <span>Total do Pedido</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── ABA: LOGÍSTICA ───────────────────────────────── --}}
                @if($activeTab === 'logistica')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Logística e Entrega
                    </h3>
                    <div class="nx-so-grid-2">
                        <div class="nx-field">
                            <label>Tipo de Frete</label>
                            <select wire:model="freight_type">
                                <option value="">— Selecione —</option>
                                @foreach($fretes as $f)
                                    <option value="{{ $f->value }}">{{ $f->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Transportadora</label>
                            <select wire:model="carrier_id">
                                <option value="">— Sem transportadora —</option>
                                @foreach($carriers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="nx-field">
                        <label>Endereço de Entrega</label>
                        <textarea wire:model="delivery_address" rows="3" placeholder="Rua, número, bairro, cidade – UF, CEP..."></textarea>
                    </div>
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
                        <label>Observação Interna</label>
                        <textarea wire:model="notes" rows="4" placeholder="Observações internas (não enviadas ao fornecedor)..."></textarea>
                    </div>
                    <div class="nx-field">
                        <label>Observação para o Fornecedor</label>
                        <textarea wire:model="notes_supplier" rows="4" placeholder="Instruções ou informações adicionais para o fornecedor..."></textarea>
                    </div>
                </div>
                @endif

            </form>{{-- end form --}}

            {{-- Modal Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">
                    Cancelar
                </button>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:12px;color:#94A3B8;">
                        Total: <strong style="color:#0F172A;">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                    </span>
                    <button type="button" wire:click="save" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ $editingId ? 'Salvar Alterações' : 'Criar Pedido' }}
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
    @if($showDetail && $viewingOrder)
    <div class="nx-so-modal-wrap" wire:click.self="closeDetail">
        <div class="nx-so-modal" style="max-width:780px;">

            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                        <strong style="font-family:monospace;font-size:16px;color:#6366F1;">{{ $viewingOrder->order_number }}</strong>
                        <span class="nx-so-badge {{ $viewingOrder->status->badgeClass() }}">{{ $viewingOrder->status->label() }}</span>
                    </div>
                    <p style="font-size:13px;color:#64748B;margin:0;">
                        {{ $viewingOrder->supplier?->social_name ?? $viewingOrder->supplier?->name }}
                        &nbsp;·&nbsp; {{ $viewingOrder->order_date?->format('d/m/Y H:i') }}
                    </p>
                </div>
                <button type="button" wire:click="closeDetail"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-so-modal-body">
                {{-- Info do pedido --}}
                <div class="nx-so-detail-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Comprador</span>
                        <strong>{{ $viewingOrder->buyer?->name ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Origem</span>
                        <strong>{{ $viewingOrder->origin?->label() ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Previsão de Entrega</span>
                        <strong>{{ $viewingOrder->expected_delivery_date?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Pagamento</span>
                        <strong>{{ $viewingOrder->payment_condition ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Forma de Pgto</span>
                        <strong>{{ $viewingOrder->payment_method ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Frete</span>
                        <strong>{{ $viewingOrder->freight_type ?? '—' }}</strong>
                    </div>
                </div>

                {{-- Itens --}}
                <h4 style="font-size:13px;font-weight:700;color:#0F172A;margin-bottom:10px;">Itens do Pedido</h4>
                <div class="nx-table-wrap" style="margin-bottom:16px;">
                    <table class="nx-table" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descrição</th>
                                <th>SKU</th>
                                <th>Unid.</th>
                                <th class="nx-th-right">Qtd</th>
                                <th class="nx-th-right">Preço Unit.</th>
                                <th class="nx-th-right">Desconto</th>
                                <th class="nx-th-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingOrder->items as $i => $item)
                            <tr>
                                <td style="color:#94A3B8;">{{ $i+1 }}</td>
                                <td>{{ $item->description }}</td>
                                <td style="font-family:monospace;color:#64748B;">{{ $item->sku ?? '—' }}</td>
                                <td>{{ $item->unit }}</td>
                                <td class="nx-td-right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                                <td class="nx-td-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="nx-td-right" style="color:#EF4444;">R$ {{ number_format($item->discount, 2, ',', '.') }}</td>
                                <td class="nx-td-right" style="font-weight:700;">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totais --}}
                <div class="nx-so-totals-box">
                    <div class="nx-so-totals-row"><span>Subtotal</span><span>R$ {{ number_format($viewingOrder->subtotal, 2, ',', '.') }}</span></div>
                    <div class="nx-so-totals-row"><span>(-) Desconto</span><span style="color:#EF4444">- R$ {{ number_format($viewingOrder->discount_amount, 2, ',', '.') }}</span></div>
                    <div class="nx-so-totals-row"><span>(+) Frete</span><span>R$ {{ number_format($viewingOrder->shipping_amount, 2, ',', '.') }}</span></div>
                    <div class="nx-so-totals-row"><span>(+) Outras Despesas</span><span>R$ {{ number_format($viewingOrder->other_expenses, 2, ',', '.') }}</span></div>
                    <div class="nx-so-totals-divider"></div>
                    <div class="nx-so-totals-final"><span>Total</span><span>R$ {{ number_format($viewingOrder->total_amount, 2, ',', '.') }}</span></div>
                </div>

                @if($viewingOrder->notes)
                    <div style="margin-top:14px;padding:12px 14px;background:#F8FAFC;border-radius:8px;border-left:3px solid #CBD5E1;">
                        <strong style="font-size:12px;color:#64748B;">Obs. Interna:</strong>
                        <p style="font-size:13px;color:#475569;margin:4px 0 0;">{{ $viewingOrder->notes }}</p>
                    </div>
                @endif
                @if($viewingOrder->notes_supplier)
                    <div style="margin-top:10px;padding:12px 14px;background:#FFF7ED;border-radius:8px;border-left:3px solid #FED7AA;">
                        <strong style="font-size:12px;color:#92400E;">Obs. para Fornecedor:</strong>
                        <p style="font-size:13px;color:#78350F;margin:4px 0 0;">{{ $viewingOrder->notes_supplier }}</p>
                    </div>
                @endif
            </div>

            {{-- Detail Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-ghost">Fechar</button>
                <div style="display:flex;gap:8px;">
                    @if($viewingOrder->status === \App\Enums\PurchaseOrderStatus::AguardandoAprovacao)
                        <button type="button" wire:click="approve({{ $viewingOrder->id }})"
                            class="nx-btn" style="background:#10B981;color:#fff;border-color:#10B981;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Aprovar
                        </button>
                    @endif
                    @if(!in_array($viewingOrder->status, [\App\Enums\PurchaseOrderStatus::Cancelado, \App\Enums\PurchaseOrderStatus::RecebidoTotal]))
                        <button type="button" wire:click="edit({{ $viewingOrder->id }})" class="nx-btn nx-btn-outline">
                            Editar
                        </button>
                        <button type="button"
                            wire:click="cancelOrder({{ $viewingOrder->id }})"
                            wire:confirm="Tem certeza que deseja cancelar este pedido?"
                            class="nx-btn" style="background:#FEF2F2;color:#DC2626;border-color:rgba(239,68,68,0.3);">
                            Cancelar Pedido
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

</div>

