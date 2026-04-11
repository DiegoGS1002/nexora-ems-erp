<div class="nx-so-page">
    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'vendas') }}" class="nx-breadcrumb-link">Vendas</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Pedidos de Venda</span>
            </nav>
            <h1 class="nx-page-title">Pedidos de Venda</h1>
            <p class="nx-page-subtitle">Controle completo de orçamentos e pedidos fiscais</p>
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
        <div class="nx-kpi-card" style="border-top:3px solid #3B82F6">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Abertos</p>
                    <p class="nx-kpi-card-value" style="color:#3B82F6">{{ $stats['aberto'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.08);color:#3B82F6;border-color:rgba(59,130,246,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #10B981">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Aprovados</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['approved'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #F59E0B">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Em Separação</p>
                    <p class="nx-kpi-card-value" style="color:#F59E0B">{{ $stats['em_separacao'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(245,158,11,0.08);color:#F59E0B;border-color:rgba(245,158,11,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 8h14M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM5 8v11a1 1 0 0 0 1 1h3m10-12a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm0 0v11a1 1 0 0 1-1 1H9m0 0a1 1 0 0 1-1-1v-1m1 1a1 1 0 0 0 1-1v-1m0 0h-1m1 0H9"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #059669">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Valor Total</p>
                    <p class="nx-kpi-card-value" style="color:#059669;font-size:16px">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(5,150,105,0.08);color:#059669;border-color:rgba(5,150,105,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
    </div>
    {{-- ── FILTERS ──────────────────────────────────── --}}
    <div class="nx-op-filters">
        <div class="nx-search-wrap" style="max-width:300px;flex:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar nº pedido ou cliente..." class="nx-search">
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
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Canal</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th class="nx-th-right">Itens</th>
                    <th class="nx-th-right">Total</th>
                    <th>Data</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr wire:key="so-{{ $order->id }}">
                        <td><strong style="font-family:monospace;color:#6366F1;">{{ $order->order_number }}</strong></td>
                        <td>
                            <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $order->client?->name ?? 'N/A' }}</div>
                            @if($order->client_cpf_cnpj)
                                <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $order->client_cpf_cnpj }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#475569;">{{ $order->seller?->name ?? '—' }}</td>
                        <td>
                            @if($order->sales_channel)
                                <span style="font-size:11px;font-weight:600;padding:3px 7px;border-radius:5px;background:#F1F5F9;color:#475569;">
                                    {{ $order->sales_channel?->label() ?? $order->sales_channel }}
                                </span>
                            @else
                                <span style="color:#CBD5E1">—</span>
                            @endif
                        </td>
                        <td><span class="nx-op-badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                        <td>
                            @if($order->is_fiscal)
                                <span style="color:#06B6D4;font-size:11px;font-weight:700;background:rgba(6,182,212,0.1);padding:3px 7px;border-radius:5px;">FISCAL</span>
                            @else
                                <span style="color:#94A3B8;font-size:11px;font-weight:600;background:#F8FAFC;padding:3px 7px;border-radius:5px;">GERENCIAL</span>
                            @endif
                        </td>
                        <td class="nx-td-right" style="font-size:12px;color:#64748B;">{{ $order->items->count() }}</td>
                        <td class="nx-td-right" style="font-weight:700;color:#059669;font-family:monospace;">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                        <td style="font-size:11px;color:#94A3B8;">
                            <div>{{ $order->order_date?->format('d/m/Y') ?? $order->created_at->format('d/m/Y') }}</div>
                            @if($order->expected_delivery_date)
                                <div style="color:#F59E0B;">Prev: {{ $order->expected_delivery_date->format('d/m') }}</div>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <button wire:click="openDetail({{ $order->id }})" class="nx-action-btn" title="Visualizar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button wire:click="edit({{ $order->id }})" class="nx-action-btn" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button wire:click="cancelOrder({{ $order->id }})" wire:confirm="Cancelar este pedido?" class="nx-action-btn nx-action-danger" title="Cancelar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="nx-td-empty">Nenhum pedido encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $orders->links() }}
            </div>
        @endif
        </div>{{-- /.nx-table-wrap --}}
    </div>{{-- /.nx-card --}}
    {{-- ══════════════════════════════════════════
         MODAL FORM — NOVO / EDITAR PEDIDO
    ══════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeModal">
        <div class="nx-so-modal">
            {{-- Header --}}
            <div class="nx-op-modal-header">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="nx-op-modal-icon" style="background:rgba(99,102,241,0.1);color:#6366F1;border-color:rgba(99,102,241,0.2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div>
                        <p class="nx-op-modal-title">{{ $editingId ? 'Editar Pedido' : 'Novo Pedido de Venda' }}</p>
                        <p class="nx-op-modal-subtitle">Preencha as informações do pedido nas abas abaixo</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            {{-- Tabs --}}
            <div class="nx-so-tabs">
                @php
                    $tabs = [
                        ['key'=>'geral',      'label'=>'Geral',        'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                        ['key'=>'itens',      'label'=>'Itens',        'icon'=>'<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>'],
                        ['key'=>'enderecos',  'label'=>'Endereços',    'icon'=>'<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>'],
                        ['key'=>'pagamento',  'label'=>'Pagamento',    'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                        ['key'=>'logistica',  'label'=>'Logística',    'icon'=>'<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
                        ['key'=>'fiscal',     'label'=>'Fiscal',       'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                        ['key'=>'totais',     'label'=>'Totais',       'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                        ['key'=>'obs',        'label'=>'Observações',  'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <button type="button" wire:click="setTab('{{ $tab['key'] }}')"
                        class="nx-so-tab {{ $activeTab === $tab['key'] ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $tab['icon'] !!}</svg>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
            <form wire:submit.prevent="save">
                <div class="nx-so-modal-body">
                    {{-- ══ TAB: GERAL ══════════════════════════════════════ --}}
                    @if($activeTab === 'geral')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Identificação do Pedido
                        </div>
                        <div class="nx-so-grid-3">
                            <div class="nx-field">
                                <label>Status do Pedido</label>
                                <select wire:model="status">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-field">
                                <label>Tipo de Operação</label>
                                <select wire:model="operation_type">
                                    <option value="">Selecione...</option>
                                    @foreach($operacoes as $op)
                                        <option value="{{ $op->value }}">{{ $op->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-field">
                                <label>Canal de Venda</label>
                                <select wire:model="sales_channel">
                                    <option value="">Selecione...</option>
                                    @foreach($canais as $c)
                                        <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="nx-so-grid-3">
                            <div class="nx-field">
                                <label>Origem do Pedido</label>
                                <select wire:model="origin">
                                    <option value="">Selecione...</option>
                                    @foreach($origens as $o)
                                        <option value="{{ $o->value }}">{{ $o->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-field">
                                <label>Empresa / Filial</label>
                                <input type="text" wire:model="company_branch" placeholder="Ex: Matriz, Filial SP">
                            </div>
                            <div class="nx-field">
                                <label style="display:flex;align-items:center;gap:8px;">
                                    Tipo de Pedido
                                </label>
                                <label style="display:flex;align-items:center;gap:10px;margin-top:10px;cursor:pointer;">
                                    <input type="checkbox" wire:model="is_fiscal" style="width:18px;height:18px;accent-color:#06B6D4;">
                                    <span style="font-size:13px;font-weight:600;color:#06B6D4;">Pedido Fiscal (NF-e)</span>
                                </label>
                            </div>
                        </div>
                        {{-- Tipo de Operação Fiscal --}}
                        @if($is_fiscal)
                        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 16px;margin-top:4px;margin-bottom:4px;">
                            <div style="font-size:12px;font-weight:600;color:#1D4ED8;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                Tipo de Operação Fiscal (CFOP / CST)
                            </div>
                            <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                                <div class="nx-field" style="flex:1;min-width:220px;margin-bottom:0;">
                                    <select wire:model.live="tipo_operacao_fiscal_id" style="border-color:#BFDBFE;background:#fff;">
                                        <option value="">— Selecione o tipo de operação —</option>
                                        @foreach($tiposOperacaoFiscal as $top)
                                            <option value="{{ $top->id }}">
                                                {{ $top->codigo }} – {{ $top->descricao }}
                                                @if($top->cfop) · CFOP {{ $top->cfop }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($tipo_operacao_fiscal_id)
                                    @php $topSelected = $tiposOperacaoFiscal->find($tipo_operacao_fiscal_id); @endphp
                                    @if($topSelected && count($orderItems) > 0)
                                    <button type="button" wire:click="applyTipoOperacao"
                                        style="padding:8px 14px;background:#1D4ED8;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        Aplicar a todos os itens
                                    </button>
                                    @endif
                                @endif
                            </div>
                            @if($tipo_operacao_fiscal_id)
                                @php $topSelected = $tiposOperacaoFiscal->find($tipo_operacao_fiscal_id); @endphp
                                @if($topSelected)
                                <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:10px;font-size:11px;color:#1E40AF;">
                                    @if($topSelected->cfop)
                                        <span><strong>CFOP:</strong> {{ $topSelected->cfop }}</span>
                                    @endif
                                    @if($topSelected->icms_cst)
                                        <span><strong>CST ICMS:</strong> {{ $topSelected->icms_cst }} @if($topSelected->icms_aliquota)({{ $topSelected->icms_aliquota }}%)@endif</span>
                                    @endif
                                    @if($topSelected->ipi_cst)
                                        <span><strong>CST IPI:</strong> {{ $topSelected->ipi_cst }} @if($topSelected->ipi_aliquota)({{ $topSelected->ipi_aliquota }}%)@endif</span>
                                    @endif
                                    @if($topSelected->pis_cst)
                                        <span><strong>PIS:</strong> {{ $topSelected->pis_cst }} @if($topSelected->pis_aliquota)({{ $topSelected->pis_aliquota }}%)@endif</span>
                                    @endif
                                    @if($topSelected->cofins_cst)
                                        <span><strong>COFINS:</strong> {{ $topSelected->cofins_cst }} @if($topSelected->cofins_aliquota)({{ $topSelected->cofins_aliquota }}%)@endif</span>
                                    @endif
                                    @if($topSelected->natureza_operacao)
                                        <span><strong>Natureza:</strong> {{ $topSelected->natureza_operacao }}</span>
                                    @endif
                                </div>
                                @endif
                            @endif
                            @if(session()->has('fiscal_applied'))
                                <div style="margin-top:8px;font-size:12px;color:#15803D;font-weight:600;">
                                    ✓ {{ session('fiscal_applied') }}
                                </div>
                            @endif
                        </div>
                        @endif
                        <div class="nx-so-section-title" style="margin-top:20px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Dados do Cliente e Vendedor
                        </div>
                        <div class="nx-so-grid-2">
                            <div class="nx-field">
                                <label>Cliente *</label>
                                <select wire:model.live="client_id">
                                    <option value="">Selecione o cliente...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} — {{ $client->taxNumber }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Vendedor / Representante</label>
                                <select wire:model="seller_id">
                                    <option value="">Selecione o vendedor...</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Info do cliente selecionado --}}
                        @if($client_id)
                            @php $selectedClient = $clients->find($client_id); @endphp
                            @if($selectedClient)
                            <div class="nx-so-client-info">
                                <div class="nx-so-client-info-item">
                                    <span class="nx-so-client-info-label">CNPJ/CPF</span>
                                    <span>{{ $selectedClient->taxNumber }}</span>
                                </div>
                                <div class="nx-so-client-info-item">
                                    <span class="nx-so-client-info-label">Situação</span>
                                    <span style="color:{{ $selectedClient->situation === 'active' ? '#10B981' : '#EF4444' }}">
                                        {{ $selectedClient->situation === 'active' ? 'Ativo' : ($selectedClient->situation === 'defaulter' ? '⚠️ Inadimplente' : $selectedClient->situation) }}
                                    </span>
                                </div>
                                <div class="nx-so-client-info-item">
                                    <span class="nx-so-client-info-label">Limite de Crédito</span>
                                    <span>R$ {{ number_format($selectedClient->credit_limit ?? 0, 2, ',', '.') }}</span>
                                </div>
                                <div class="nx-so-client-info-item">
                                    <span class="nx-so-client-info-label">Cond. Padrão</span>
                                    <span>{{ $selectedClient->payment_condition_default ?? '—' }}</span>
                                </div>
                            </div>
                            @endif
                        @endif
                        <div class="nx-so-section-title" style="margin-top:20px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Datas
                        </div>
                        <div class="nx-so-grid-3">
                            <div class="nx-field">
                                <label>Data do Pedido</label>
                                <input type="datetime-local" wire:model="order_date">
                            </div>
                            <div class="nx-field">
                                <label>Previsão de Entrega</label>
                                <input type="date" wire:model="expected_delivery_date">
                            </div>
                            <div class="nx-field">
                                <label>Data de Entrega Real</label>
                                <input type="date" wire:model="delivery_date">
                            </div>
                        </div>
                        <div class="nx-so-section-title" style="margin-top:20px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Pagamento e Tabela de Preço
                        </div>
                        <div class="nx-so-grid-2">
                            <div class="nx-field">
                                <label>Condição de Pagamento</label>
                                <input type="text" wire:model="payment_condition" placeholder="Ex: 30/60/90 dias, À vista">
                            </div>
                            <div class="nx-field">
                                <label>Tabela de Preço</label>
                                <select wire:model="price_table_id">
                                    <option value="">Preço padrão</option>
                                    @foreach($priceTables as $pt)
                                        <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- ══ TAB: ITENS ═══════════════════════════════════════ --}}
                    @if($activeTab === 'itens')
                    <div class="nx-so-tab-content">
                        @error('orderItems') <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#DC2626;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;">{{ $message }}</div> @enderror
                        {{-- Busca de produto --}}
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Adicionar Produto
                        </div>
                        <div class="nx-field" style="position:relative;margin-bottom:8px;">
                            <input type="text" wire:model.live.debounce.300ms="searchProduct"
                                placeholder="Buscar por nome, SKU ou EAN..."
                                style="width:100%;padding:10px 14px;border:2px solid #E2E8F0;border-radius:10px;font-size:13px;outline:none;transition:border-color 0.2s;"
                                onfocus="this.style.borderColor='#6366F1'"
                                onblur="this.style.borderColor='#E2E8F0'">
                        </div>
                        @if(count($searchResults) > 0)
                            <div class="nx-so-search-results">
                                @foreach($searchResults as $prod)
                                    <button type="button" wire:click="addProduct('{{ $prod['id'] }}')" class="nx-so-search-result-item">
                                        <div>
                                            <span style="font-weight:600;color:#1E293B;">{{ $prod['name'] }}</span>
                                            <span style="color:#94A3B8;font-size:11px;margin-left:8px;">SKU: {{ $prod['product_code'] ?? '—' }}</span>
                                            @if($prod['ean'] ?? null)
                                                <span style="color:#94A3B8;font-size:11px;margin-left:6px;">| EAN: {{ $prod['ean'] }}</span>
                                            @endif
                                        </div>
                                        <span style="color:#6366F1;font-weight:700;font-family:monospace;font-size:13px;">R$ {{ number_format($prod['sale_price'] ?? 0, 2, ',', '.') }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        {{-- Lista de itens --}}
                        @if(count($orderItems) > 0)
                            <div class="nx-so-section-title" style="margin-top:20px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                Itens do Pedido ({{ count($orderItems) }})
                            </div>
                            @foreach($orderItems as $idx => $item)
                                <div class="nx-so-item-card" wire:key="item-{{ $idx }}">
                                    <div class="nx-so-item-header">
                                        <div style="display:flex;align-items:center;gap:10px;flex:1">
                                            <span class="nx-so-item-num">{{ $idx + 1 }}</span>
                                            <div>
                                                <div style="font-size:13px;font-weight:700;color:#1E293B;">{{ $item['product_name'] }}</div>
                                                <div style="font-size:11px;color:#94A3B8;">
                                                    SKU: {{ $item['sku'] ?: '—' }}
                                                    @if($item['ean']) | EAN: {{ $item['ean'] }} @endif
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="removeItem({{ $idx }})" style="background:none;border:none;cursor:pointer;color:#EF4444;padding:4px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                    <div class="nx-so-item-fields">
                                        <div class="nx-field">
                                            <label>Unidade</label>
                                            <select wire:model="orderItems.{{ $idx }}.unit">
                                                @foreach(['UN','KG','G','L','ML','CX','PC','M','M2','M3','TON'] as $u)
                                                    <option value="{{ $u }}">{{ $u }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="nx-field">
                                            <label>Quantidade</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.quantity" step="0.001" min="0.001">
                                            @error("orderItems.{$idx}.quantity") <small style="color:#EF4444">{{ $message }}</small> @enderror
                                        </div>
                                        <div class="nx-field">
                                            <label>Preço Unitário</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.unit_price" step="0.01" min="0">
                                            @error("orderItems.{$idx}.unit_price") <small style="color:#EF4444">{{ $message }}</small> @enderror
                                        </div>
                                        <div class="nx-field">
                                            <label>Desc. (%)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.discount_percent" step="0.01" min="0" max="100">
                                        </div>
                                        <div class="nx-field">
                                            <label>Desc. (R$)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.discount" step="0.01" min="0">
                                        </div>
                                        <div class="nx-field">
                                            <label>Acréscimo</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.addition" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="nx-so-item-total">
                                        @php
                                            $qty   = (float) ($item['quantity'] ?? 0);
                                            $price = (float) ($item['unit_price'] ?? 0);
                                            $disc  = (float) ($item['discount'] ?? 0);
                                            $discP = (float) ($item['discount_percent'] ?? 0);
                                            $add   = (float) ($item['addition'] ?? 0);
                                            if ($discP > 0) $disc = ($qty * $price) * ($discP / 100);
                                            $itemTotal = ($qty * $price) - $disc + $add;
                                        @endphp
                                        <span style="font-size:11px;color:#94A3B8;">Subtotal do item:</span>
                                        <span style="font-size:15px;font-weight:800;color:#059669;font-family:monospace;">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                            {{-- Resumo de itens --}}
                            <div class="nx-so-items-summary">
                                <span style="color:#64748B;font-size:13px;">{{ count($orderItems) }} produto(s)</span>
                                <span style="font-size:16px;font-weight:800;color:#059669;font-family:monospace;">
                                    Subtotal: R$ {{ number_format($subtotal, 2, ',', '.') }}
                                </span>
                            </div>
                        @else
                            <div class="nx-so-empty-items">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                <p>Nenhum produto adicionado</p>
                                <p style="font-size:12px;color:#CBD5E1">Use a busca acima para adicionar produtos</p>
                            </div>
                        @endif
                    </div>
                    @endif
                    {{-- ══ TAB: ENDEREÇOS ════════════════════════════════════ --}}
                    @if($activeTab === 'enderecos')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Endereço de Faturamento
                        </div>
                        @include('livewire.vendas._address-fields', ['prefix' => 'billing', 'addr' => $billing])
                        <div style="display:flex;align-items:center;gap:10px;margin:20px 0 16px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:#475569;">
                                <input type="checkbox" wire:model.live="same_billing_delivery" style="width:16px;height:16px;accent-color:#6366F1;">
                                Endereço de entrega igual ao de faturamento
                            </label>
                        </div>
                        @if(!$same_billing_delivery)
                            <div class="nx-so-section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                Endereço de Entrega
                            </div>
                            @include('livewire.vendas._address-fields', ['prefix' => 'delivery', 'addr' => $delivery])
                        @endif
                    </div>
                    @endif
                    {{-- ══ TAB: PAGAMENTO ═══════════════════════════════════ --}}
                    @if($activeTab === 'pagamento')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Forma de Pagamento
                        </div>
                        <div class="nx-so-grid-2">
                            <div class="nx-field">
                                <label>Condição de Pagamento</label>
                                <input type="text" wire:model="payment_condition" placeholder="Ex: À vista, 30/60/90 dias">
                            </div>
                            <div class="nx-field">
                                <label>Forma de Pagamento</label>
                                <select wire:model="payment_method">
                                    <option value="">Selecione...</option>
                                    @foreach(['Dinheiro','Cartão de Crédito','Cartão de Débito','PIX','Boleto','Transferência','Cheque','Outros'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="nx-so-grid-2">
                            <div class="nx-field">
                                <label>Número de Parcelas</label>
                                <select wire:model="installments_qty">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ $i }}x</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @if((int)$installments_qty > 1 && $totalGeral > 0)
                            <div class="nx-so-section-title" style="margin-top:20px">
                                Simulação de Parcelas
                            </div>
                            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;overflow:hidden;">
                                <table class="nx-table" style="margin:0;">
                                    <thead><tr><th>Parcela</th><th class="nx-th-right">Valor</th><th>Vencimento Previsto</th></tr></thead>
                                    <tbody>
                                        @for($i = 1; $i <= (int)$installments_qty; $i++)
                                            <tr>
                                                <td>{{ $i }}ª</td>
                                                <td class="nx-td-right" style="font-family:monospace;font-weight:700;">R$ {{ number_format($totalGeral / (int)$installments_qty, 2, ',', '.') }}</td>
                                                <td style="font-size:12px;color:#64748B;">{{ now()->addDays(30 * $i)->format('d/m/Y') }}</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    @endif
                    {{-- ══ TAB: LOGÍSTICA ════════════════════════════════════ --}}
                    @if($activeTab === 'logistica')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            Transportadora e Frete
                        </div>
                        <div class="nx-so-grid-2">
                            <div class="nx-field">
                                <label>Transportadora</label>
                                <select wire:model="carrier_id">
                                    <option value="">Sem transportadora</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-field">
                                <label>Tipo de Frete</label>
                                <select wire:model="freight_type">
                                    <option value="">Selecione...</option>
                                    @foreach($tiposFrete as $tf)
                                        <option value="{{ $tf->value }}">{{ $tf->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="nx-so-grid-4">
                            <div class="nx-field">
                                <label>Peso Bruto (kg)</label>
                                <input type="number" wire:model="gross_weight" step="0.001" min="0" placeholder="0,000">
                            </div>
                            <div class="nx-field">
                                <label>Peso Líquido (kg)</label>
                                <input type="number" wire:model="net_weight" step="0.001" min="0" placeholder="0,000">
                            </div>
                            <div class="nx-field">
                                <label>Volumes</label>
                                <input type="number" wire:model="volumes" min="1" placeholder="1">
                            </div>
                            <div class="nx-field">
                                <label>Cód. Rastreio</label>
                                <input type="text" wire:model="tracking_code" placeholder="Ex: BR12345678910">
                            </div>
                        </div>
                        <div class="nx-field">
                            <label>Observações de Entrega</label>
                            <textarea wire:model="delivery_notes" rows="3" placeholder="Instruções especiais para entrega..."></textarea>
                        </div>
                    </div>
                    @endif
                    {{-- ══ TAB: FISCAL ═══════════════════════════════════════ --}}
                    @if($activeTab === 'fiscal')
                    <div class="nx-so-tab-content">
                        @if(!$is_fiscal)
                            <div class="nx-so-empty-items" style="padding:30px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <p>Pedido não fiscal</p>
                                <p style="font-size:12px;color:#CBD5E1">Ative "Pedido Fiscal (NF-e)" na aba Geral para habilitar os campos fiscais</p>
                                <button type="button" wire:click="setTab('geral')" class="nx-btn nx-btn-outline nx-btn-sm" style="margin-top:12px;">Ir para Geral</button>
                            </div>
                        @else
                            {{-- Tipo de Operação Fiscal selecionado --}}
                            @if($tipo_operacao_fiscal_id)
                                @php $topFiscal = $tiposOperacaoFiscal->find($tipo_operacao_fiscal_id); @endphp
                                @if($topFiscal)
                                <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#15803D;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                        <strong>{{ $topFiscal->codigo }}</strong> — {{ $topFiscal->descricao }}
                                        @if($topFiscal->cfop) · CFOP <strong>{{ $topFiscal->cfop }}</strong>@endif
                                    </div>
                                    @if(count($orderItems) > 0)
                                    <button type="button" wire:click="applyTipoOperacao"
                                        style="padding:5px 12px;background:#15803D;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        ↻ Reaplicar a todos os itens
                                    </button>
                                    @endif
                                </div>
                                @endif
                            @else
                                <div style="background:#FEF9C3;border:1px solid #FDE047;border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#854D0E;display:flex;align-items:center;gap:8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Nenhum tipo de operação fiscal selecionado. Selecione na aba <strong>Geral</strong> para pré-preencher CFOP e CST automaticamente.
                                </div>
                            @endif
                            @if(count($orderItems) === 0)
                                <div class="nx-so-empty-items" style="padding:30px;">
                                    <p>Adicione produtos na aba Itens para configurar os dados fiscais</p>
                                    <button type="button" wire:click="setTab('itens')" class="nx-btn nx-btn-outline nx-btn-sm" style="margin-top:12px;">Ir para Itens</button>
                                </div>
                            @else
                                @foreach($orderItems as $idx => $item)
                                <div class="nx-so-fiscal-card" wire:key="fiscal-{{ $idx }}">
                                    <div class="nx-so-fiscal-card-title">
                                        <span class="nx-so-item-num">{{ $idx + 1 }}</span>
                                        {{ $item['product_name'] }}
                                    </div>
                                    <div class="nx-so-grid-4" style="margin-top:12px">
                                        <div class="nx-field">
                                            <label>CFOP</label>
                                            <input type="text" wire:model="orderItems.{{ $idx }}.cfop" placeholder="5102" maxlength="5">
                                        </div>
                                        <div class="nx-field">
                                            <label>NCM</label>
                                            <input type="text" wire:model="orderItems.{{ $idx }}.ncm" placeholder="00000000" maxlength="8">
                                        </div>
                                        <div class="nx-field">
                                            <label>CST</label>
                                            <input type="text" wire:model="orderItems.{{ $idx }}.cst" placeholder="00" maxlength="3">
                                        </div>
                                        <div class="nx-field">
                                            <label>CSOSN</label>
                                            <input type="text" wire:model="orderItems.{{ $idx }}.csosn" placeholder="102" maxlength="3">
                                        </div>
                                    </div>
                                    <div class="nx-so-grid-4" style="margin-top:8px">
                                        <div class="nx-field">
                                            <label>Origem Merc.</label>
                                            <select wire:model="orderItems.{{ $idx }}.origin">
                                                <option value="0">0 - Nacional</option>
                                                <option value="1">1 - Estrangeira (Importação direta)</option>
                                                <option value="2">2 - Estrangeira (Adquirida no mercado interno)</option>
                                                <option value="3">3 - Nacional com importação superior a 40%</option>
                                                <option value="4">4 - Nacional (processos produtivos básicos)</option>
                                                <option value="5">5 - Nacional com importação inferior a 40%</option>
                                                <option value="6">6 - Estrangeira (Importação direta, sem similar)</option>
                                                <option value="7">7 - Estrangeira (adquirida no mercado interno)</option>
                                                <option value="8">8 - Nacional com importação superior a 70%</option>
                                            </select>
                                        </div>
                                        <div class="nx-field">
                                            <label>ICMS (%)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.icms_percent" step="0.01" min="0" max="100" placeholder="0,00">
                                        </div>
                                        <div class="nx-field">
                                            <label>IPI (%)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.ipi_percent" step="0.01" min="0" max="100" placeholder="0,00">
                                        </div>
                                        <div class="nx-field">
                                            <label>PIS (%)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.pis_percent" step="0.01" min="0" max="100" placeholder="0,65">
                                        </div>
                                    </div>
                                    <div class="nx-so-grid-4" style="margin-top:8px">
                                        <div class="nx-field">
                                            <label>COFINS (%)</label>
                                            <input type="number" wire:model="orderItems.{{ $idx }}.cofins_percent" step="0.01" min="0" max="100" placeholder="3,00">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    @endif
                    {{-- ══ TAB: TOTAIS ═══════════════════════════════════════ --}}
                    @if($activeTab === 'totais')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            Ajustes de Valor
                        </div>
                        <div class="nx-so-grid-3">
                            <div class="nx-field">
                                <label>Desconto Geral (R$)</label>
                                <input type="number" wire:model="discount_amount" step="0.01" min="0" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Acréscimos (R$)</label>
                                <input type="number" wire:model="additions_amount" step="0.01" min="0" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Frete (R$)</label>
                                <input type="number" wire:model="shipping_amount" step="0.01" min="0" placeholder="0,00">
                            </div>
                        </div>
                        <div class="nx-so-grid-3">
                            <div class="nx-field">
                                <label>Seguro (R$)</label>
                                <input type="number" wire:model="insurance_amount" step="0.01" min="0" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Outras Despesas (R$)</label>
                                <input type="number" wire:model="other_expenses" step="0.01" min="0" placeholder="0,00">
                            </div>
                        </div>
                        {{-- Resumo financeiro --}}
                        <div class="nx-so-totals-box">
                            <div class="nx-so-totals-row">
                                <span>Subtotal dos produtos</span>
                                <span style="font-family:monospace;">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                            @if((float)$discount_amount > 0)
                            <div class="nx-so-totals-row" style="color:#DC2626">
                                <span>(-) Desconto</span>
                                <span style="font-family:monospace;">- R$ {{ number_format($discount_amount, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            @if((float)$additions_amount > 0)
                            <div class="nx-so-totals-row" style="color:#059669">
                                <span>(+) Acréscimos</span>
                                <span style="font-family:monospace;">+ R$ {{ number_format($additions_amount, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            @if((float)$shipping_amount > 0)
                            <div class="nx-so-totals-row">
                                <span>(+) Frete</span>
                                <span style="font-family:monospace;">+ R$ {{ number_format($shipping_amount, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            @if((float)$insurance_amount > 0)
                            <div class="nx-so-totals-row">
                                <span>(+) Seguro</span>
                                <span style="font-family:monospace;">+ R$ {{ number_format($insurance_amount, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            @if((float)$other_expenses > 0)
                            <div class="nx-so-totals-row">
                                <span>(+) Outras despesas</span>
                                <span style="font-family:monospace;">+ R$ {{ number_format($other_expenses, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="nx-so-totals-divider"></div>
                            <div class="nx-so-totals-final">
                                <span>TOTAL DO PEDIDO</span>
                                <span>R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- ══ TAB: OBSERVAÇÕES ════════════════════════════════════ --}}
                    @if($activeTab === 'obs')
                    <div class="nx-so-tab-content">
                        <div class="nx-so-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            Observações
                        </div>
                        <div class="nx-field">
                            <label>Observação Interna</label>
                            <textarea wire:model="internal_notes" rows="3" placeholder="Apenas para uso interno (não aparece para o cliente)..."></textarea>
                        </div>
                        <div class="nx-field">
                            <label>Observação para o Cliente</label>
                            <textarea wire:model="customer_notes" rows="3" placeholder="Informações que aparecerão no pedido do cliente..."></textarea>
                        </div>
                        <div class="nx-field">
                            <label>Observação Fiscal <span style="color:#94A3B8;font-size:11px;">(será incluída na NF-e)</span></label>
                            <textarea wire:model="fiscal_notes_obs" rows="3" placeholder="Dados complementares para nota fiscal..."></textarea>
                        </div>
                    </div>
                    @endif
                </div>{{-- end nx-so-modal-body --}}
                {{-- FOOTER --}}
                <div class="nx-so-modal-footer">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-size:13px;color:#64748B;">{{ count($orderItems) }} produto(s)</span>
                        <span style="font-size:15px;font-weight:800;color:#059669;font-family:monospace;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="button" wire:click="closeModal" class="nx-btn nx-btn-outline">Cancelar</button>
                        <button type="submit" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <span wire:loading.remove>Salvar Pedido</span>
                            <span wire:loading>Salvando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    {{-- ══════════════════════════════════════════
         MODAL DETALHE DO PEDIDO
    ══════════════════════════════════════════ --}}
    @if($showDetail && $this->viewingOrder)
    @php $vo = $this->viewingOrder; @endphp
    <div class="nx-so-modal-wrap" wire:click.self="closeDetail">
        <div class="nx-so-modal" style="max-width:860px;">
            <div class="nx-op-modal-header">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="nx-op-modal-icon" style="background:rgba(99,102,241,0.1);color:#6366F1;border-color:rgba(99,102,241,0.2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div>
                        <p class="nx-op-modal-title" style="font-family:monospace;font-size:18px;">{{ $vo->order_number }}</p>
                        <p class="nx-op-modal-subtitle">{{ $vo->client?->name }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDetail" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-so-modal-body">
                {{-- Status e badges --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
                    <span class="nx-op-badge {{ $vo->status->badgeClass() }}" style="font-size:13px;padding:5px 12px;">{{ $vo->status->label() }}</span>
                    @if($vo->is_fiscal)
                        <span style="color:#06B6D4;font-size:12px;font-weight:700;background:rgba(6,182,212,0.1);padding:5px 10px;border-radius:6px;">📄 FISCAL</span>
                    @endif
                    @if($vo->operation_type)
                        <span style="background:#F1F5F9;color:#475569;font-size:12px;padding:5px 10px;border-radius:6px;font-weight:600;">{{ $vo->operation_type?->label() ?? $vo->operation_type }}</span>
                    @endif
                    @if($vo->sales_channel)
                        <span style="background:#F1F5F9;color:#475569;font-size:12px;padding:5px 10px;border-radius:6px;">🏪 {{ $vo->sales_channel?->label() ?? $vo->sales_channel }}</span>
                    @endif
                    @if($vo->needs_approval && $vo->status->value === 'aberto')
                        <span style="background:rgba(245,158,11,0.1);color:#D97706;font-size:12px;padding:5px 10px;border-radius:6px;font-weight:700;">⚠️ Aguardando Aprovação</span>
                    @endif
                </div>
                {{-- Dados principais --}}
                <div class="nx-so-detail-grid">
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Cliente</span>
                        <span class="nx-so-detail-value">{{ $vo->client?->name }}</span>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">CPF/CNPJ</span>
                        <span class="nx-so-detail-value" style="font-family:monospace;">{{ $vo->client_cpf_cnpj ?? $vo->client?->taxNumber ?? '—' }}</span>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Vendedor</span>
                        <span class="nx-so-detail-value">{{ $vo->seller?->name ?? '—' }}</span>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Data do Pedido</span>
                        <span class="nx-so-detail-value">{{ $vo->order_date?->format('d/m/Y H:i') ?? $vo->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Prev. Entrega</span>
                        <span class="nx-so-detail-value">{{ $vo->expected_delivery_date?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Cond. Pagamento</span>
                        <span class="nx-so-detail-value">{{ $vo->payment_condition ?? '—' }}</span>
                    </div>
                    @if($vo->carrier)
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Transportadora</span>
                        <span class="nx-so-detail-value">{{ $vo->carrier->name }}</span>
                    </div>
                    @endif
                    @if($vo->freight_type)
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Frete</span>
                        <span class="nx-so-detail-value">{{ $vo->freight_type?->label() ?? $vo->freight_type }}</span>
                    </div>
                    @endif
                </div>
                {{-- Endereços --}}
                @if($vo->addresses->count() > 0)
                <div style="margin-top:16px;">
                    <h4 style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">Endereços</h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        @foreach($vo->addresses as $addr)
                        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;padding:12px;">
                            <p style="font-size:11px;font-weight:700;color:#6366F1;text-transform:uppercase;margin:0 0 6px;">
                                {{ $addr->type === 'billing' ? '🏢 Faturamento' : ($addr->type === 'delivery' ? '🚚 Entrega' : '📋 Cobrança') }}
                            </p>
                            <p style="font-size:12px;color:#475569;margin:0;line-height:1.5;">
                                {{ $addr->street }}, {{ $addr->number }}
                                @if($addr->complement) - {{ $addr->complement }} @endif
                                <br>{{ $addr->district }} — {{ $addr->city }}/{{ $addr->state }}
                                <br>CEP: {{ $addr->zip_code }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                {{-- Itens --}}
                @if($vo->items->count() > 0)
                <h4 style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.05em;margin:20px 0 10px;">Itens do Pedido</h4>
                <table class="nx-table" style="margin:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th>Un</th>
                            <th class="nx-th-right">Qtd</th>
                            <th class="nx-th-right">Preço</th>
                            <th class="nx-th-right">Desc.</th>
                            <th class="nx-th-right">Total</th>
                            @if($vo->is_fiscal)<th>CFOP</th><th>NCM</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vo->items as $i => $item)
                        <tr>
                            <td style="color:#94A3B8;font-size:11px;">{{ $i+1 }}</td>
                            <td style="font-weight:600;font-size:13px;">{{ $item->description ?: $item->product?->name }}</td>
                            <td style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $item->sku ?: '—' }}</td>
                            <td style="font-size:12px;">{{ $item->unit }}</td>
                            <td class="nx-td-right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                            <td class="nx-td-right" style="font-family:monospace;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="nx-td-right" style="color:#DC2626;font-size:12px;">
                                @if($item->discount > 0) R$ {{ number_format($item->discount, 2, ',', '.') }} @else — @endif
                            </td>
                            <td class="nx-td-right" style="font-weight:700;color:#059669;font-family:monospace;">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            @if($vo->is_fiscal)
                            <td style="font-size:11px;font-family:monospace;">{{ $item->cfop }}</td>
                            <td style="font-size:11px;font-family:monospace;">{{ $item->ncm }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                {{-- Totais --}}
                <div class="nx-so-totals-box" style="margin-top:16px;">
                    <div class="nx-so-totals-row">
                        <span>Subtotal</span>
                        <span style="font-family:monospace;">R$ {{ number_format($vo->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($vo->discount_amount > 0)
                    <div class="nx-so-totals-row" style="color:#DC2626;">
                        <span>(-) Desconto</span>
                        <span style="font-family:monospace;">- R$ {{ number_format($vo->discount_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($vo->shipping_amount > 0)
                    <div class="nx-so-totals-row">
                        <span>(+) Frete</span>
                        <span style="font-family:monospace;">+ R$ {{ number_format($vo->shipping_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="nx-so-totals-divider"></div>
                    <div class="nx-so-totals-final">
                        <span>TOTAL</span>
                        <span>R$ {{ number_format($vo->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
                {{-- Observações --}}
                @if($vo->internal_notes || $vo->customer_notes || $vo->fiscal_notes_obs)
                <div style="margin-top:16px;display:grid;gap:10px;">
                    @if($vo->internal_notes)
                    <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;padding:12px;">
                        <p style="font-size:11px;font-weight:700;color:#C2410C;margin:0 0 4px;">📌 OBSERVAÇÃO INTERNA</p>
                        <p style="font-size:13px;color:#431407;margin:0;">{{ $vo->internal_notes }}</p>
                    </div>
                    @endif
                    @if($vo->customer_notes)
                    <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:12px;">
                        <p style="font-size:11px;font-weight:700;color:#15803D;margin:0 0 4px;">💬 OBSERVAÇÃO PARA O CLIENTE</p>
                        <p style="font-size:13px;color:#14532D;margin:0;">{{ $vo->customer_notes }}</p>
                    </div>
                    @endif
                    @if($vo->fiscal_notes_obs)
                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px;">
                        <p style="font-size:11px;font-weight:700;color:#1D4ED8;margin:0 0 4px;">📄 OBSERVAÇÃO FISCAL</p>
                        <p style="font-size:13px;color:#1E3A8A;margin:0;">{{ $vo->fiscal_notes_obs }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <div class="nx-so-modal-footer">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @if($vo->status->value === 'aberto' || $vo->status->value === 'draft')
                        <button type="button" wire:click="approve({{ $vo->id }})" class="nx-btn" style="background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.2);font-size:13px;">
                            ✅ Aprovar
                        </button>
                    @endif
                    @if(!in_array($vo->status->value, ['cancelled','invoiced']))
                        <button type="button" wire:click="cancelOrder({{ $vo->id }})" wire:confirm="Cancelar este pedido?" class="nx-btn" style="background:rgba(239,68,68,0.08);color:#DC2626;border:1px solid rgba(239,68,68,0.2);font-size:13px;">
                            ✖ Cancelar Pedido
                        </button>
                    @endif
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-outline">Fechar</button>
                    <button type="button" wire:click="edit({{ $vo->id }})" class="nx-btn nx-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
