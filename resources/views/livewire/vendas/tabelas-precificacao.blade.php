<div class="nx-so-page">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'vendas') }}" class="nx-breadcrumb-link" wire:navigate>Vendas</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Tabelas de Precificação</span>
            </nav>
            <h1 class="nx-page-title">Tabelas de Precificação</h1>
            <p class="nx-page-subtitle">Gestão de preços e cálculo de markup</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openTableModal" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Tabela
            </button>
            <button type="button" wire:click="openCalculator" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="16" y2="18"/></svg>
                Calculadora de Preços
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

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div class="nx-op-kpis" style="grid-template-columns:repeat(3,minmax(0,1fr))">
        <div class="nx-kpi-card" style="border-top:3px solid #06B6D4">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Tabelas de Preço</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(6,182,212,0.08);color:#06B6D4;border-color:rgba(6,182,212,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #10B981">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Tabelas Ativas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['active'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #6366F1">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Produtos Precificados</p>
                    <p class="nx-kpi-card-value" style="color:#6366F1">{{ $stats['products_with_price'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTERS ──────────────────────────────────── --}}
    <div class="nx-op-filters">
        <div class="nx-search-wrap" style="max-width:320px;flex:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar tabela..." class="nx-search">
        </div>
        <select wire:model.live="filterActive" class="nx-filter-select">
            <option value="">Todos</option>
            <option value="1">Ativas</option>
            <option value="0">Inativas</option>
        </select>

        {{-- Working Price Table Selector --}}
        <div style="flex:1;max-width:350px;">
            <select wire:model.live="working_price_table_id" class="nx-filter-select" style="border:2px solid #6366F1;font-weight:600;color:#4F46E5;">
                <option value="">📋 Selecione tabela para precificação...</option>
                @foreach($this->activePriceTables as $table)
                    <option value="{{ $table->id }}">
                        {{ $table->name }} ({{ $table->code }})
                        @if($table->is_default) ⭐ Padrão @endif
                    </option>
                @endforeach
            </select>
            @if($workingPriceTable)
                <p style="font-size:11px;color:#6366F1;margin:4px 0 0;font-weight:600;">
                    ✓ Tabela Ativa: {{ $workingPriceTable->name }}
                </p>
            @endif
        </div>

        @if($search || $filterActive !== '')
            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm" style="color:#EF4444;border-color:rgba(239,68,68,0.3)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Limpar
            </button>
        @endif
    </div>

    {{-- ── PRICE TABLE ITEMS (quando tabela selecionada) ──────── --}}
    @if($workingPriceTable)
        <div class="nx-card" style="margin-bottom:24px;">
            <div style="padding:20px;border-bottom:1px solid #F1F5F9;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:#0F172A;margin:0 0 4px;">
                            📊 Produtos na Tabela: {{ $workingPriceTable->name }}
                        </h3>
                        <p style="font-size:12px;color:#64748B;margin:0;">
                            Produtos com preços definidos nesta tabela
                        </p>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:13px;color:#64748B;">
                            {{ $priceTableItems->total() }} {{ $priceTableItems->total() === 1 ? 'produto' : 'produtos' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($priceTableItems->count() > 0)
                <div class="nx-table-wrap">
                    <table class="nx-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Produto</th>
                                <th class="nx-th-right">Preço na Tabela</th>
                                <th class="nx-th-right">Preço Mínimo</th>
                                <th class="nx-th-right">Preço Base Produto</th>
                                <th class="nx-th-right">Diferença</th>
                                <th>Última Atualização</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceTableItems as $item)
                                <tr wire:key="item-{{ $item->id }}">
                                    <td>
                                        <strong style="font-family:monospace;color:#6366F1;font-size:12px;">
                                            {{ $item->product?->product_code ?? '—' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <div style="font-size:13px;font-weight:600;color:#1E293B;">
                                            {{ $item->product?->name ?? 'Produto não encontrado' }}
                                        </div>
                                        @if($item->product?->ean)
                                            <div style="font-size:11px;color:#94A3B8;">EAN: {{ $item->product->ean }}</div>
                                        @endif
                                    </td>
                                    <td class="nx-td-right">
                                        <strong style="font-size:14px;color:#059669;">
                                            R$ {{ number_format($item->price, 2, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td class="nx-td-right" style="font-size:12px;color:#F59E0B;">
                                        R$ {{ number_format($item->minimum_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="nx-td-right" style="font-size:13px;color:#64748B;">
                                        R$ {{ number_format($item->product?->sale_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="nx-td-right">
                                        @php
                                            $diff = (float)$item->price - (float)($item->product?->sale_price ?? 0);
                                        @endphp
                                        <span style="font-size:12px;font-weight:600;color:{{ $diff >= 0 ? '#059669' : '#DC2626' }};">
                                            {{ $diff >= 0 ? '+' : '' }}R$ {{ number_format($diff, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td style="font-size:12px;color:#64748B;">
                                        {{ $item->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($priceTableItems->hasPages())
                    <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                        {{ $priceTableItems->links() }}
                    </div>
                @endif
            @else
                <div style="padding:60px 20px;text-align:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin:0 auto 16px;">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    <h3 style="font-size:14px;font-weight:600;color:#64748B;margin:0 0 8px;">Nenhum produto nesta tabela</h3>
                    <p style="font-size:12px;color:#94A3B8;margin:0;">Use a calculadora para adicionar produtos com preços calculados</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ── TABLE ────────────────────────────────────── --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Vigência</th>
                        <th class="nx-th-right">Produtos</th>
                        <th>Status</th>
                        <th>Padrão</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tables as $table)
                        <tr wire:key="table-{{ $table->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;">{{ $table->code }}</strong>
                            </td>
                            <td style="font-size:13px;font-weight:600;color:#1E293B;">{{ $table->name }}</td>
                            <td style="font-size:12px;color:#64748B;">{{ $table->description ?? '—' }}</td>
                            <td style="font-size:12px;color:#64748B;">
                                @if($table->valid_from || $table->valid_until)
                                    {{ $table->valid_from?->format('d/m/Y') ?? '—' }} até {{ $table->valid_until?->format('d/m/Y') ?? '—' }}
                                @else
                                    Indeterminado
                                @endif
                            </td>
                            <td class="nx-td-right" style="font-size:13px;color:#475569;">{{ $table->items_count ?? 0 }}</td>
                            <td>
                                <button type="button" wire:click="toggleActive({{ $table->id }})"
                                    class="nx-so-badge {{ $table->is_active ? 'nx-so-badge--approved' : 'nx-so-badge--cancelled' }}"
                                    style="cursor:pointer;">
                                    {{ $table->is_active ? 'Ativa' : 'Inativa' }}
                                </button>
                            </td>
                            <td>
                                @if($table->is_default)
                                    <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:5px;background:#DBEAFE;color:#1D4ED8;">
                                        Padrão
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" wire:click="editTable({{ $table->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Editar tabela">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    @if($table->items_count === 0)
                                        <button type="button"
                                            wire:click="deleteTable({{ $table->id }})"
                                            wire:confirm="Tem certeza que deseja excluir esta tabela?"
                                            class="nx-btn nx-btn-ghost nx-btn-sm"
                                            style="color:#EF4444;"
                                            title="Excluir tabela">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                @include('partials.empty-state', [
                                    'title'       => 'Nenhuma tabela de preço encontrada',
                                    'description' => 'As tabelas de preço já criadas aparecerão aqui.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tables->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $tables->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — CALCULADORA DE PREÇOS
    ══════════════════════════════════════════════════════════════ --}}
    @if($showCalculator)
    <div class="nx-so-modal-wrap" wire:click.self="closeCalculator">
        <div class="nx-so-modal" style="max-width:1000px;">

            {{-- Modal Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                <div>
                    <h2 style="font-size:18px;font-weight:700;color:#0F172A;margin:0;">
                        💰 Calculadora de Precificação
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:4px 0 0;">
                        Método de markup divisor para formação de preço de venda
                    </p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button type="button" wire:click="resetCalculator" class="nx-btn nx-btn-outline nx-btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        Resetar
                    </button>
                    <button type="button" wire:click="closeCalculator"
                        style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="nx-so-modal-body" style="max-height:calc(90vh - 180px);overflow-y:auto;">

                {{-- Product Selection --}}
                @if(!$selected_product_id)
                    <div style="background:#F0F9FF;border:2px dashed #0EA5E9;border-radius:12px;padding:20px;margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0EA5E9" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                            <div>
                                <h5 style="font-size:14px;font-weight:700;color:#0369A1;margin:0;">Selecione um Produto (Opcional)</h5>
                                <p style="font-size:12px;color:#0284C7;margin:2px 0 0;">Carregue automaticamente o custo e compare com o preço atual</p>
                            </div>
                        </div>
                        <div style="position:relative;">
                            <div class="nx-search-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" wire:model.live.debounce.300ms="searchProductCalc"
                                    placeholder="Buscar produto por nome, código ou EAN..." class="nx-search">
                            </div>
                            @if(count($searchProductResults))
                                <div class="nx-so-search-results">
                                    @foreach($searchProductResults as $prod)
                                        <div class="nx-so-search-result-item" wire:click="selectProduct('{{ $prod['id'] }}')">
                                            <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $prod['name'] }}</div>
                                            <div style="font-size:11px;color:#94A3B8;">
                                                @if($prod['product_code']) Cód: {{ $prod['product_code'] }} @endif
                                                @if($prod['ean']) · EAN: {{ $prod['ean'] }} @endif
                                                · Custo: R$ {{ number_format($prod['cost_price'] ?? 0, 2, ',', '.') }}
                                                · Venda: R$ {{ number_format($prod['sale_price'] ?? 0, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div style="background:linear-gradient(135deg, #0EA5E9 0%, #06B6D4 100%);border-radius:12px;padding:16px;margin-bottom:20px;color:#fff;">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="flex:1;">
                                <div style="font-size:11px;font-weight:600;opacity:0.9;margin-bottom:4px;">PRODUTO SELECIONADO</div>
                                <div style="font-size:15px;font-weight:700;">{{ $selectedProductData['name'] ?? '' }}</div>
                                <div style="font-size:11px;opacity:0.85;margin-top:2px;">
                                    @if($selectedProductData['product_code'] ?? null) Código: {{ $selectedProductData['product_code'] }} @endif
                                </div>
                            </div>
                            <button type="button" wire:click="clearSelectedProduct"
                                style="width:28px;height:28px;border-radius:6px;border:none;background:rgba(255,255,255,0.2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:24px;">

                    {{-- LEFT: Input Fields --}}
                    <div>
                        {{-- Custos --}}
                        <div style="background:#F8FAFC;border-radius:12px;padding:16px;margin-bottom:16px;border:1px solid #E2E8F0;">
                            <h4 style="font-size:13px;font-weight:700;color:#475569;margin:0 0 12px;text-transform:uppercase;letter-spacing:.5px;">
                                📦 Custos do Produto
                            </h4>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Custo Matéria-Prima (R$)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="custo_materia_prima">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Despesas (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="despesas">
                                </div>
                            </div>
                        </div>

                        {{-- Impostos e Custos Comerciais --}}
                        <div style="background:#F8FAFC;border-radius:12px;padding:16px;margin-bottom:16px;border:1px solid #E2E8F0;">
                            <h4 style="font-size:13px;font-weight:700;color:#475569;margin:0 0 12px;text-transform:uppercase;letter-spacing:.5px;">
                                🧾 Impostos e Custos sobre Venda (%)
                            </h4>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Impostos (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="imposto">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Comissão (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="comissao">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Frete (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="frete">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Prazo (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="prazo">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>VPC - Variáveis (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="vpc">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Assistência (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="assistencia">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Inadimplência (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="inadimplencia">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label style="color:#059669;font-weight:700;">💰 Lucro Desejado (%)</label>
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="lucro" style="border-color:#059669;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Results --}}
                    <div>
                        @if(isset($resultado['error']))
                            <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:10px;padding:16px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    <strong style="font-size:14px;color:#991B1B;">Erro no Cálculo</strong>
                                </div>
                                <p style="font-size:13px;color:#7F1D1D;margin:0;">{{ $resultado['error'] }}</p>
                                <p style="font-size:12px;color:#991B1B;margin:8px 0 0;">
                                    Soma atual: <strong>{{ number_format($resultado['soma_percentuais'] ?? 0, 2, ',', '.') }}%</strong>
                                </p>
                            </div>
                        @else
                            {{-- Resultado Principal --}}
                            <div style="background:linear-gradient(135deg, #059669 0%, #10B981 100%);border-radius:12px;padding:20px;margin-bottom:16px;color:#fff;">
                                <div style="font-size:12px;font-weight:600;margin-bottom:4px;opacity:0.9;">PREÇO DE VENDA SUGERIDO</div>
                                <div style="font-size:36px;font-weight:800;margin-bottom:8px;">
                                    R$ {{ number_format($resultado['preco_final'] ?? 0, 2, ',', '.') }}
                                </div>
                                <div style="font-size:11px;opacity:0.85;">
                                    Índice de Preço: {{ number_format($resultado['indice_preco'] ?? 0, 4, ',', '.') }}
                                </div>
                            </div>

                            {{-- Price Comparison (if product selected) --}}
                            @if($selectedProductData)
                                <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:12px;padding:16px;margin-bottom:16px;">
                                    <h5 style="font-size:12px;font-weight:700;color:#92400E;margin:0 0 12px;text-transform:uppercase;letter-spacing:.5px;">
                                        📊 Comparação de Preços
                                    </h5>
                                    <div style="display:flex;flex-direction:column;gap:10px;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span style="font-size:12px;color:#78350F;">Preço Atual (Cadastrado)</span>
                                            <strong style="font-size:16px;color:#92400E;">R$ {{ number_format($selectedProductData['sale_price'] ?? 0, 2, ',', '.') }}</strong>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span style="font-size:12px;color:#78350F;">Preço Calculado (Sugerido)</span>
                                            <strong style="font-size:16px;color:#059669;">R$ {{ number_format($resultado['preco_final'] ?? 0, 2, ',', '.') }}</strong>
                                        </div>
                                        <div style="height:1px;background:#FCD34D;margin:4px 0;"></div>
                                        @php
                                            $currentPrice = (float) ($selectedProductData['sale_price'] ?? 0);
                                            $calculatedPrice = (float) ($resultado['preco_final'] ?? 0);
                                            $diff = $calculatedPrice - $currentPrice;
                                            $diffPercent = $currentPrice > 0 ? (($diff / $currentPrice) * 100) : 0;
                                        @endphp
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span style="font-size:12px;font-weight:600;color:#78350F;">Diferença</span>
                                            <div style="text-align:right;">
                                                <div style="font-size:14px;font-weight:700;color:{{ $diff >= 0 ? '#059669' : '#DC2626' }};">
                                                    {{ $diff >= 0 ? '+' : '' }}R$ {{ number_format($diff, 2, ',', '.') }}
                                                </div>
                                                <div style="font-size:11px;color:#92400E;">
                                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diffPercent, 2, ',', '.') }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div style="margin-top:16px;padding-top:12px;border-top:1px solid #FCD34D;">
                                        @if($workingPriceTable)
                                            <div style="background:#EEF2FF;border-radius:8px;padding:10px 12px;margin-bottom:12px;">
                                                <div style="display:flex;align-items:center;gap:8px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                                    <div style="flex:1;">
                                                        <div style="font-size:10px;color:#6366F1;font-weight:600;margin-bottom:2px;">TABELA SELECIONADA</div>
                                                        <div style="font-size:13px;color:#4338CA;font-weight:700;">{{ $workingPriceTable->name }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div style="background:#FEE2E2;border-radius:8px;padding:10px 12px;margin-bottom:12px;text-align:center;">
                                                <div style="font-size:11px;color:#991B1B;font-weight:600;">
                                                    ⚠️ Selecione uma tabela de preços na página principal
                                                </div>
                                            </div>
                                        @endif

                                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                            <button type="button" wire:click="saveToPriceTable"
                                                class="nx-btn nx-btn-outline"
                                                style="border-color:#6366F1;color:#6366F1;font-size:12px;padding:8px 12px;"
                                                wire:loading.attr="disabled"
                                                @if(!$workingPriceTable) disabled style="opacity:0.5;cursor:not-allowed;border-color:#CBD5E1;color:#94A3B8;" @endif>
                                                <span wire:loading.remove wire:target="saveToPriceTable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                                    Salvar na Tabela
                                                </span>
                                                <span wire:loading wire:target="saveToPriceTable">Salvando...</span>
                                            </button>

                                            <button type="button" wire:click="updateProductPrice"
                                                class="nx-btn nx-btn-primary"
                                                style="background:#059669;border-color:#059669;font-size:12px;padding:8px 12px;"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="updateProductPrice">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                                    Atualizar Produto
                                                </span>
                                                <span wire:loading wire:target="updateProductPrice">Atualizando...</span>
                                            </button>
                                        </div>

                                        <p style="font-size:10px;color:#92400E;margin:8px 0 0;text-align:center;">
                                            💡 "Salvar na Tabela" não altera o preço base do produto
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Breakdown --}}
                            <div style="background:#F8FAFC;border-radius:12px;padding:16px;margin-bottom:12px;border:1px solid #E2E8F0;">
                                <h5 style="font-size:12px;font-weight:700;color:#475569;margin:0 0 12px;text-transform:uppercase;letter-spacing:.5px;">
                                    Composição de Custos
                                </h5>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <div style="display:flex;justify-content:space-between;font-size:12px;">
                                        <span style="color:#64748B;">Custo do Produto</span>
                                        <strong style="color:#0F172A;">R$ {{ number_format($resultado['custo_produto'] ?? 0, 2, ',', '.') }}</strong>
                                    </div>
                                    <div style="height:1px;background:#E2E8F0;"></div>
                                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                                        <span style="color:#94A3B8;">Impostos</span>
                                        <span style="color:#64748B;">R$ {{ number_format($resultado['valor_imposto'] ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                                        <span style="color:#94A3B8;">Comissão</span>
                                        <span style="color:#64748B;">R$ {{ number_format($resultado['valor_comissao'] ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                                        <span style="color:#94A3B8;">Frete</span>
                                        <span style="color:#64748B;">R$ {{ number_format($resultado['valor_frete'] ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                                        <span style="color:#94A3B8;">Prazo + VPC + Assist. + Inadimp.</span>
                                        <span style="color:#64748B;">R$ {{ number_format(($resultado['valor_prazo'] ?? 0) + ($resultado['valor_vpc'] ?? 0) + ($resultado['valor_assistencia'] ?? 0) + ($resultado['valor_inadimplencia'] ?? 0), 2, ',', '.') }}</span>
                                    </div>
                                    <div style="height:1px;background:#E2E8F0;margin:4px 0;"></div>
                                    <div style="display:flex;justify-content:space-between;font-size:12px;">
                                        <strong style="color:#059669;">Lucro Líquido</strong>
                                        <strong style="color:#059669;">R$ {{ number_format($resultado['valor_lucro'] ?? 0, 2, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Indicadores --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                <div style="background:#FFF7ED;border-radius:10px;padding:12px;border:1px solid #FED7AA;">
                                    <div style="font-size:10px;color:#92400E;font-weight:600;margin-bottom:4px;">MARGEM REAL</div>
                                    <div style="font-size:20px;font-weight:700;color:#C2410C;">{{ number_format($resultado['margem_real'] ?? 0, 2, ',', '.') }}%</div>
                                </div>
                                <div style="background:#ECFDF5;border-radius:10px;padding:12px;border:1px solid #A7F3D0;">
                                    <div style="font-size:10px;color:#065F46;font-weight:600;margin-bottom:4px;">PREÇO MÍNIMO</div>
                                    <div style="font-size:16px;font-weight:700;color:#047857;">R$ {{ number_format($resultado['preco_minimo'] ?? 0, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — CREATE/EDIT PRICE TABLE
    ══════════════════════════════════════════════════════════════ --}}
    @if($showTableModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeTableModal">
        <div class="nx-so-modal" style="max-width:600px;">

            {{-- Modal Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#0F172A;margin:0;">
                        {{ $editingTableId ? 'Editar Tabela de Preços' : 'Nova Tabela de Preços' }}
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                        {{ $editingTableId ? 'Atualize as informações da tabela' : 'Crie uma nova tabela para gerenciar preços' }}
                    </p>
                </div>
                <button type="button" wire:click="closeTableModal"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form wire:submit="saveTable" class="nx-so-modal-body">

                @if($errors->any())
                    <div class="alert-error" style="margin-bottom:16px;">
                        <strong>Corrija os erros:</strong>
                        <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="nx-so-grid-2">
                    <div class="nx-field">
                        <label>Nome da Tabela <span style="color:#EF4444">*</span></label>
                        <input type="text" wire:model="table_name" placeholder="Ex: Atacado, Varejo, Promoção...">
                        @error('table_name')<span class="nx-field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="nx-field">
                        <label>Código <span style="color:#EF4444">*</span></label>
                        <input type="text" wire:model="table_code" placeholder="Ex: TAB-001, ATACADO" style="text-transform:uppercase;">
                        @error('table_code')<span class="nx-field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="nx-field">
                    <label>Descrição</label>
                    <textarea wire:model="table_description" rows="3" placeholder="Descrição opcional da tabela de preços..."></textarea>
                </div>

                <div class="nx-so-grid-2">
                    <div class="nx-field">
                        <label>Válida De</label>
                        <input type="date" wire:model="table_valid_from">
                    </div>
                    <div class="nx-field">
                        <label>Válida Até</label>
                        <input type="date" wire:model="table_valid_until">
                    </div>
                </div>

                <div style="display:flex;gap:16px;padding:16px;background:#F8FAFC;border-radius:8px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" wire:model="table_is_active" style="width:18px;height:18px;cursor:pointer;">
                        <span style="font-size:13px;font-weight:600;color:#475569;">Tabela Ativa</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" wire:model="table_is_default" style="width:18px;height:18px;cursor:pointer;">
                        <span style="font-size:13px;font-weight:600;color:#475569;">Tabela Padrão</span>
                    </label>
                </div>

            </form>

            {{-- Modal Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeTableModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="saveTable" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveTable">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ $editingTableId ? 'Salvar Alterações' : 'Criar Tabela' }}
                    </span>
                    <span wire:loading wire:target="saveTable">Salvando...</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>

