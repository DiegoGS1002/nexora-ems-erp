<div class="nx-op-page">

    {{-- PAGE HEADER --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'producao') }}" class="nx-breadcrumb-link">Produção</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Ordens de Produção</span>
            </nav>
            <h1 class="nx-page-title">Ordens de Produção</h1>
            <p class="nx-page-subtitle">Gerencie e acompanhe o chão de fábrica em tempo real</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova OP
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @session('message')
        <div class="nx-op-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-op-kpis">
        <div class="nx-kpi-card nx-op-card-total">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de OPs</p>
                    <p class="nx-kpi-card-value">{{ number_format($stats['total'], 0, ',', '.') }}</p>
                    <span class="nx-kpi-card-trend">Todas as ordens</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card nx-op-card-planned">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Planejadas</p>
                    <p class="nx-kpi-card-value" style="color:#6366F1">{{ number_format($stats['planned'], 0, ',', '.') }}</p>
                    <span class="nx-kpi-card-trend">Aguardando início</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card nx-op-card-progress">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Em Produção</p>
                    <p class="nx-kpi-card-value" style="color:#F59E0B">{{ number_format($stats['in_progress'], 0, ',', '.') }}</p>
                    <span class="nx-kpi-card-trend is-positive">Ativas agora</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(245,158,11,0.08);color:#F59E0B;border-color:rgba(245,158,11,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card nx-op-card-done">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Finalizadas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ number_format($stats['completed'], 0, ',', '.') }}</p>
                    <span class="nx-kpi-card-trend">Concluídas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-op-filters">
        <div class="nx-search-wrap" style="max-width:300px;flex:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar por produto, OP ou lote..." class="nx-search">
        </div>
        <select wire:model.live="filterStatus" class="nx-filter-select">
            <option value="">Todos os Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>
        @if($search || $filterStatus)
            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm" style="color:#EF4444;border-color:rgba(239,68,68,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Limpar
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         KANBAN BOARD
    ══════════════════════════════════════════════════ --}}
    @php
        use App\Enums\ProductionOrderStatus;
        $columns = [
            ['status' => ProductionOrderStatus::Planned,    'color' => '#6366F1'],
            ['status' => ProductionOrderStatus::InProgress, 'color' => '#F59E0B'],
            ['status' => ProductionOrderStatus::Completed,  'color' => '#10B981'],
        ];
    @endphp

    <div class="nx-op-kanban">
        @foreach($columns as $col)
            @php $colOrders = $orders->filter(fn($o) => $o->status === $col['status']); @endphp
            <div class="nx-op-column">
                <div class="nx-op-column-header" style="--col-color:{{ $col['color'] }}">
                    <div class="nx-op-column-title-wrap">
                        <span class="nx-op-column-dot" style="background:{{ $col['color'] }}"></span>
                        <h3 class="nx-op-column-title">{{ $col['status']->label() }}</h3>
                    </div>
                    <span class="nx-op-column-count" style="background:{{ $col['color'] }}1a;color:{{ $col['color'] }}">
                        {{ $colOrders->count() }}
                    </span>
                </div>
                <div class="nx-op-cards">
                    @forelse($colOrders as $order)
                        @include('livewire.producao._op-card', ['order' => $order])
                    @empty
                        <div class="nx-op-column-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <p>Nenhuma OP aqui</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach

        {{-- Coluna Pausadas + Canceladas --}}
        @php $extraOrders = $orders->filter(fn($o) => in_array($o->status, [ProductionOrderStatus::Paused, ProductionOrderStatus::Cancelled])); @endphp
        <div class="nx-op-column">
            <div class="nx-op-column-header" style="--col-color:#94A3B8">
                <div class="nx-op-column-title-wrap">
                    <span class="nx-op-column-dot" style="background:#94A3B8"></span>
                    <h3 class="nx-op-column-title">Pausadas / Canceladas</h3>
                </div>
                <span class="nx-op-column-count" style="background:rgba(148,163,184,0.15);color:#64748B">
                    {{ $extraOrders->count() }}
                </span>
            </div>
            <div class="nx-op-cards">
                @forelse($extraOrders as $order)
                    @include('livewire.producao._op-card', ['order' => $order])
                @empty
                    <div class="nx-op-column-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <p>Nenhuma OP aqui</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MODAL CRIAR / EDITAR OP
    ═══════════════════════════════════════════ --}}
    @if($showModal)
        <div class="nx-op-modal-wrap" wire:click.self="closeModal">
            <div class="nx-op-modal">
                <div class="nx-op-modal-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="nx-op-modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22V8"/><path d="m20 12-8-4-8 4"/><path d="M20 17v-5"/><path d="M4 17v-5"/><path d="M20 22v-5"/><path d="M4 22v-5"/></svg>
                        </div>
                        <div>
                            <p class="nx-op-modal-title">{{ $editingId ? 'Editar Ordem de Produção' : 'Nova Ordem de Produção' }}</p>
                            <p class="nx-op-modal-subtitle">Defina os produtos, quantidades e insumos necessários</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="nx-op-modal-body">

                        {{-- ── DADOS GERAIS ── --}}
                        <div class="nx-op-form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Dados Gerais
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                            <div class="nx-field">
                                <label>Nome / Referência da OP</label>
                                <input type="text" wire:model="name" placeholder="Ex: OP Camiseta Maio/2026">
                            </div>
                            <div class="nx-field">
                                <label>Número do Lote</label>
                                <input type="text" wire:model="lot_number" placeholder="Ex: LOTE-2026-05">
                                @error('lot_number') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
                            <div class="nx-field">
                                <label>Data de Início</label>
                                <input type="datetime-local" wire:model="start_date">
                                @error('start_date') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Previsão de Término</label>
                                <input type="datetime-local" wire:model="end_date">
                                @error('end_date') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Custo Estimado (R$)</label>
                                <input type="number" wire:model="estimated_cost" step="0.01" min="0" placeholder="0,00">
                                @error('estimated_cost') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="nx-field">
                            <label>Observações</label>
                            <textarea wire:model="notes" rows="2" placeholder="Informações adicionais sobre esta ordem..."></textarea>
                        </div>

                        {{-- ── PRODUTOS A FABRICAR ── --}}
                        <div class="nx-op-form-section-title" style="margin-top:4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                            Produtos a Fabricar
                            <span style="font-size:10.5px;color:#EF4444;font-weight:600;text-transform:none;letter-spacing:0;">* Obrigatório adicionar ao menos 1</span>
                        </div>

                        @error('formProducts') <small style="color:#EF4444;display:block;margin-bottom:6px;">{{ $message }}</small> @enderror

                        @if(count($formProducts) > 0)
                            <div class="nx-op-items-table">
                                <div class="nx-op-items-header">
                                    <span style="flex:1">Produto</span>
                                    <span style="width:150px;text-align:right">Qtd. Planejada</span>
                                    <span style="width:36px"></span>
                                </div>
                                @foreach($formProducts as $idx => $prod)
                                    <div class="nx-op-items-row" wire:key="prod-{{ $idx }}">
                                        <select wire:model="formProducts.{{ $idx }}.product_id" style="flex:1;">
                                            <option value="">Selecione o produto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}@if($product->product_code) — {{ $product->product_code }}@endif</option>
                                            @endforeach
                                        </select>
                                        <input type="number" wire:model="formProducts.{{ $idx }}.target_quantity"
                                               step="0.001" min="0.001" placeholder="0.000"
                                               style="width:150px;text-align:right;">
                                        <button type="button" wire:click="removeFormProduct({{ $idx }})"
                                                class="nx-op-item-remove" title="Remover"
                                                {{ count($formProducts) === 1 ? 'disabled' : '' }}
                                                style="{{ count($formProducts) === 1 ? 'opacity:.35;cursor:not-allowed;' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                    @error("formProducts.{$idx}.product_id") <small style="color:#EF4444;display:block;padding:2px 12px;">{{ $message }}</small> @enderror
                                    @error("formProducts.{$idx}.target_quantity") <small style="color:#EF4444;display:block;padding:2px 12px;">{{ $message }}</small> @enderror
                                @endforeach
                            </div>
                        @endif

                        <button type="button" wire:click="addFormProduct" class="nx-op-add-item-btn" style="border-color:#A5B4FC;color:#6366F1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar outro Produto
                        </button>

                        {{-- ── INSUMOS / BOM ── --}}
                        <div class="nx-op-form-section-title" style="margin-top:4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Insumos / Lista de Materiais (BOM)
                        </div>

                        @if(count($formItems) > 0)
                            <div class="nx-op-items-table">
                                <div class="nx-op-items-header">
                                    <span style="flex:1">Componente / Matéria-prima</span>
                                    <span style="width:150px;text-align:right">Qtd. Necessária</span>
                                    <span style="width:36px"></span>
                                </div>
                                @foreach($formItems as $idx => $item)
                                    <div class="nx-op-items-row" wire:key="item-{{ $idx }}">
                                        <select wire:model="formItems.{{ $idx }}.component_id" style="flex:1;">
                                            <option value="">Selecione o insumo...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" wire:model="formItems.{{ $idx }}.required_qty"
                                               step="0.001" min="0.001" placeholder="0.000"
                                               style="width:150px;text-align:right;">
                                        <button type="button" wire:click="removeFormItem({{ $idx }})" class="nx-op-item-remove" title="Remover">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                    @error("formItems.{$idx}.component_id") <small style="color:#EF4444;display:block;padding:2px 12px;">{{ $message }}</small> @enderror
                                    @error("formItems.{$idx}.required_qty") <small style="color:#EF4444;display:block;padding:2px 12px;">{{ $message }}</small> @enderror
                                @endforeach
                            </div>
                        @endif

                        <button type="button" wire:click="addFormItem" class="nx-op-add-item-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Insumo
                        </button>

                    </div>
                    <div class="nx-op-modal-footer">
                        <button type="button" wire:click="closeModal" class="nx-btn nx-btn-outline">Cancelar</button>
                        <button type="submit" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <span wire:loading.remove>{{ $editingId ? 'Atualizar OP' : 'Criar OP' }}</span>
                            <span wire:loading>Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         MODAL PROGRESSO / PAUSA
    ═══════════════════════════════════════════ --}}
    @if($showProgressModal && $this->progressOrder)
        @php $po = $this->progressOrder; @endphp
        <div class="nx-op-modal-wrap" wire:click.self="closeProgressModal">
            <div class="nx-op-modal" style="max-width:560px;">
                <div class="nx-op-modal-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="nx-op-modal-icon" style="background:{{ $pauseAfterSave ? 'rgba(245,158,11,0.1)' : 'rgba(16,185,129,0.1)' }};color:{{ $pauseAfterSave ? '#D97706' : '#059669' }};border-color:{{ $pauseAfterSave ? 'rgba(245,158,11,0.2)' : 'rgba(16,185,129,0.2)' }}">
                            @if($pauseAfterSave)
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="nx-op-modal-title">
                                {{ $pauseAfterSave ? 'Pausar Produção' : 'Registrar Progresso' }}
                            </p>
                            <p class="nx-op-modal-subtitle">#OP-{{ str_pad($po->id, 4, '0', STR_PAD_LEFT) }} — Informe as quantidades produzidas</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeProgressModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="nx-op-modal-body" style="gap:12px;">

                    @if($pauseAfterSave)
                        <div class="nx-op-progress-notice nx-op-progress-notice--warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <span>Para pausar a OP, informe obrigatoriamente as quantidades já produzidas de cada produto.</span>
                        </div>
                    @else
                        <div class="nx-op-progress-notice nx-op-progress-notice--info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Atualize as quantidades produzidas até o momento. Os dados serão salvos sem alterar o status da OP.</span>
                        </div>
                    @endif

                    @error('progressQtys')
                        <small style="color:#EF4444">{{ $message }}</small>
                    @enderror

                    @if($po->orderProducts->count() > 0)
                        <div class="nx-op-items-table">
                            <div class="nx-op-items-header">
                                <span style="flex:1">Produto</span>
                                <span style="width:120px;text-align:center;">Meta</span>
                                <span style="width:150px;text-align:right;">Produzido até agora</span>
                            </div>
                            @foreach($po->orderProducts as $op)
                                <div class="nx-op-progress-product-row" wire:key="pq-{{ $op->id }}">
                                    <div class="nx-op-progress-product-info">
                                        <span class="nx-op-progress-product-name">{{ $op->product?->name ?? 'N/A' }}</span>
                                        <div class="nx-op-progress-track" style="height:4px;margin-top:4px;">
                                            @php $pp = $op->progress_percentage; @endphp
                                            <div class="nx-op-progress-fill" style="width:{{ $pp }}%;background:#6366F1"></div>
                                        </div>
                                    </div>
                                    <span class="nx-op-progress-meta-qty">{{ number_format($op->target_quantity, 0, ',', '.') }} UN</span>
                                    <div style="width:150px;display:flex;flex-direction:column;gap:3px;align-items:flex-end;">
                                        <input type="number"
                                               wire:model="progressQtys.{{ $op->id }}"
                                               step="0.001" min="0"
                                               max="{{ $op->target_quantity }}"
                                               placeholder="0"
                                               class="nx-op-progress-qty-input {{ $pauseAfterSave ? 'nx-op-progress-qty-input--required' : '' }}"
                                               style="width:150px;text-align:right;">
                                        @error("progressQtys.{$op->id}")
                                            <small style="color:#EF4444;font-size:10.5px;">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="nx-op-detail-no-items">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                            Nenhum produto encontrado. Edite a OP primeiro.
                        </div>
                    @endif
                </div>

                <div class="nx-op-modal-footer">
                    <button type="button" wire:click="closeProgressModal" class="nx-btn nx-btn-outline">Cancelar</button>
                    @if($po->orderProducts->count() > 0)
                        <button type="button" wire:click="saveProgress" class="nx-btn nx-btn-primary"
                                style="{{ $pauseAfterSave ? 'background:#F59E0B;border-color:#F59E0B;' : '' }}"
                                wire:loading.attr="disabled">
                            @if($pauseAfterSave)
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                <span wire:loading.remove>Pausar OP</span>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                <span wire:loading.remove>Salvar Progresso</span>
                            @endif
                            <span wire:loading>Salvando...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         MODAL DETALHES
    ═══════════════════════════════════════════ --}}
    @if($showDetail && $this->viewingOrder)
        @php $vo = $this->viewingOrder; @endphp
        <div class="nx-op-modal-wrap" wire:click.self="closeDetail">
            <div class="nx-op-modal nx-op-modal--detail">
                <div class="nx-op-modal-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="nx-op-modal-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B;border-color:rgba(245,158,11,0.2)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div>
                            <p class="nx-op-modal-title">#OP-{{ str_pad($vo->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="nx-op-modal-subtitle">{{ $vo->name ?? 'Ordem de Produção' }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetail" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="nx-op-modal-body">
                    {{-- Status + Progress Total --}}
                    <div class="nx-op-detail-status-row">
                        <span class="nx-op-badge {{ $vo->status->badgeClass() }}">{{ $vo->status->label() }}</span>
                        <div style="flex:1;margin-left:16px;">
                            <div class="nx-op-progress-header" style="margin-bottom:6px;">
                                <span style="font-size:12px;font-weight:600;color:#475569;">Progresso Total: {{ $vo->progress_percentage }}%</span>
                            </div>
                            <div class="nx-op-progress-track" style="height:8px;">
                                <div class="nx-op-progress-fill" style="width:{{ $vo->progress_percentage }}%;background:{{ $vo->status->color() }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="nx-op-detail-grid">
                        @if($vo->lot_number)
                            <div class="nx-op-detail-item"><span class="nx-op-detail-label">Lote</span><span class="nx-op-detail-value">{{ $vo->lot_number }}</span></div>
                        @endif
                        <div class="nx-op-detail-item"><span class="nx-op-detail-label">Custo Estimado</span><span class="nx-op-detail-value">R$ {{ number_format($vo->estimated_cost, 2, ',', '.') }}</span></div>
                        <div class="nx-op-detail-item"><span class="nx-op-detail-label">Data Início</span><span class="nx-op-detail-value">{{ $vo->start_date?->format('d/m/Y H:i') ?? '—' }}</span></div>
                        <div class="nx-op-detail-item"><span class="nx-op-detail-label">Previsão Término</span><span class="nx-op-detail-value">{{ $vo->end_date?->format('d/m/Y H:i') ?? '—' }}</span></div>
                        @if($vo->user)
                            <div class="nx-op-detail-item"><span class="nx-op-detail-label">Responsável</span><span class="nx-op-detail-value">{{ $vo->user->name }}</span></div>
                        @endif
                    </div>

                    @if($vo->notes)
                        <div class="nx-op-detail-notes"><span class="nx-op-detail-label">Observações</span><p>{{ $vo->notes }}</p></div>
                    @endif

                    {{-- Produtos --}}
                    @if($vo->orderProducts->count() > 0)
                        <div class="nx-op-detail-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                            Produtos a Fabricar
                        </div>
                        <div class="nx-table-wrap">
                            <table class="nx-table" style="margin:0">
                                <thead><tr><th>Produto</th><th class="nx-th-right">Meta</th><th class="nx-th-right">Produzido</th><th class="nx-th-right">Progresso</th></tr></thead>
                                <tbody>
                                    @foreach($vo->orderProducts as $op)
                                        <tr>
                                            <td style="font-weight:600;color:#0F172A;">{{ $op->product?->name ?? 'N/A' }}</td>
                                            <td class="nx-td-right">{{ number_format($op->target_quantity, 3, ',', '.') }}</td>
                                            <td class="nx-td-right" style="color:{{ $op->progress_percentage >= 100 ? '#059669' : '#475569' }};font-weight:600;">{{ number_format($op->produced_quantity, 3, ',', '.') }}</td>
                                            <td class="nx-td-right">
                                                <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                                    <div style="width:80px;height:5px;background:#F1F5F9;border-radius:99px;overflow:hidden;">
                                                        <div style="height:100%;width:{{ $op->progress_percentage }}%;background:{{ $vo->status->color() }};border-radius:99px;transition:width .4s;"></div>
                                                    </div>
                                                    <span style="font-size:12px;font-weight:700;color:#475569;font-family:monospace;">{{ $op->progress_percentage }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Insumos --}}
                    @if($vo->items->count() > 0)
                        <div class="nx-op-detail-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Lista de Insumos (BOM)
                        </div>
                        <div class="nx-table-wrap">
                            <table class="nx-table" style="margin:0">
                                <thead><tr><th>Componente</th><th class="nx-th-right">Qtd. Necessária</th><th class="nx-th-right">Qtd. Consumida</th></tr></thead>
                                <tbody>
                                    @foreach($vo->items as $item)
                                        <tr>
                                            <td style="font-weight:600;color:#0F172A;">{{ $item->component?->name ?? 'N/A' }}</td>
                                            <td class="nx-td-right">{{ number_format($item->required_qty, 3, ',', '.') }}</td>
                                            <td class="nx-td-right">{{ number_format($item->consumed_qty, 3, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="nx-op-modal-footer">
                    <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-outline">Fechar</button>
                    <button type="button" wire:click="edit({{ $vo->id }})" class="nx-btn nx-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar OP
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

