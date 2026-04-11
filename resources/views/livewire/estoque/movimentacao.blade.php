<div class="nx-mov-page">

    {{-- ══════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'estoque') }}" class="nx-breadcrumb-link">Estoque</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Movimentação de Estoque</span>
            </nav>
            <h1 class="nx-page-title">Movimentação de Estoque</h1>
            <p class="nx-page-subtitle">Rastreabilidade total de entradas e saídas do inventário</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nova Movimentação
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @session('message')
        <div class="nx-mov-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════════════
         KPI CARDS — 4 colunas iguais, alinhados
    ══════════════════════════════════════════════════ --}}
    <div class="nx-mov-kpis">

        {{-- Total Movimentações --}}
        <div class="nx-kpi-card nx-mov-card-total">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Movimentações</p>
                    <p class="nx-kpi-card-value">
                        {{ number_format($stats['total_movements'], 0, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">No período selecionado</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Entradas --}}
        <div class="nx-kpi-card nx-mov-card-input">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Entradas</p>
                    <p class="nx-kpi-card-value" style="color:#059669;">
                        + {{ number_format($stats['total_inputs'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">Unidades adicionadas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Saídas --}}
        <div class="nx-kpi-card nx-mov-card-output">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Saídas</p>
                    <p class="nx-kpi-card-value" style="color:#DC2626;">
                        - {{ number_format($stats['total_outputs'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-negative">Unidades removidas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.08);color:#EF4444;border-color:rgba(239,68,68,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <polyline points="8 8 12 12 16 8"/><line x1="12" y1="12" x2="12" y2="3"/><path d="M3.34 19.82A10 10 0 1 0 20.66 19.82"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Ajustes Manuais --}}
        <div class="nx-kpi-card nx-mov-card-adjustment">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Ajustes Manuais</p>
                    <p class="nx-kpi-card-value">
                        {{ number_format($stats['total_adjustments'], 0, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend">Correções realizadas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.08);color:#3B82F6;border-color:rgba(59,130,246,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-mov-filters">

        {{-- Intervalo de datas --}}
        <div class="nx-mov-date-group">
            <div class="nx-mov-date-field">
                <label class="nx-mov-date-label">De</label>
                <input type="date" wire:model.live="startDate" class="nx-filter-select" style="min-width:130px;">
            </div>
            <span class="nx-mov-date-sep">até</span>
            <div class="nx-mov-date-field">
                <label class="nx-mov-date-label">Até</label>
                <input type="date" wire:model.live="endDate" class="nx-filter-select" style="min-width:130px;">
            </div>
        </div>

        {{-- Busca + Selects + Limpar --}}
        <div class="nx-mov-filters-right">
            <div class="nx-search-wrap" style="max-width:280px;flex:1;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por produto..."
                       class="nx-search">
            </div>

            <select wire:model.live="filterType" class="nx-filter-select">
                <option value="">Todos os Tipos</option>
                <option value="input">Entrada</option>
                <option value="output">Saída</option>
                <option value="adjustment">Ajuste</option>
                <option value="transfer">Transferência</option>
            </select>

            <select wire:model.live="filterProduct" class="nx-filter-select">
                <option value="">Todos os Produtos</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>

            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm" style="color:#EF4444;border-color:rgba(239,68,68,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Limpar
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABELA DE MOVIMENTAÇÕES
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card">
        {{-- Cabeçalho da tabela --}}
        <div class="nx-mov-table-header">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                </svg>
                Histórico de Movimentações
            </h3>
            <span class="nx-mov-table-count">{{ $movements->total() }} registros</span>
        </div>

        {{-- Tabela --}}
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Data / Hora</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th class="nx-th-right">Quantidade</th>
                        <th>Origem / Justificativa</th>
                        <th>Usuário</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>
                                <span style="font-family:ui-monospace,monospace;font-size:12.5px;color:#475569;font-weight:500;">
                                    {{ $movement->created_at->format('d/m/Y') }}
                                </span><br>
                                <span style="font-family:ui-monospace,monospace;font-size:11px;color:#94A3B8;">
                                    {{ $movement->created_at->format('H:i') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight:600;color:#0F172A;">{{ $movement->product->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $typeMeta = [
                                        'input'      => ['class' => 'nx-mov-badge--input',      'label' => 'Entrada',       'icon' => '↑'],
                                        'output'     => ['class' => 'nx-mov-badge--output',     'label' => 'Saída',         'icon' => '↓'],
                                        'adjustment' => ['class' => 'nx-mov-badge--adjustment', 'label' => 'Ajuste',        'icon' => '⟲'],
                                        'transfer'   => ['class' => 'nx-mov-badge--transfer',   'label' => 'Transferência', 'icon' => '⇄'],
                                    ];
                                    $meta = $typeMeta[$movement->type] ?? ['class' => 'nx-mov-badge--adjustment', 'label' => 'Outro', 'icon' => '•'];
                                @endphp
                                <span class="nx-mov-badge {{ $meta['class'] }}">
                                    {{ $meta['icon'] }} {{ $meta['label'] }}
                                </span>
                            </td>
                            <td class="nx-td-right">
                                @php
                                    $qtyClass = match($movement->type) {
                                        'input'    => 'nx-mov-qty--input',
                                        'output'   => 'nx-mov-qty--output',
                                        default    => 'nx-mov-qty--other',
                                    };
                                    $qtyPrefix = $movement->type === 'input' ? '+' : ($movement->type === 'output' ? '−' : '');
                                @endphp
                                <span class="nx-mov-qty {{ $qtyClass }}">
                                    {{ $qtyPrefix }}{{ number_format($movement->quantity, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:13px;color:#64748B;font-style:italic;">{{ $movement->origin }}</span>
                            </td>
                            <td>
                                <span style="font-size:12.5px;color:#475569;">{{ $movement->user->name ?? 'N/A' }}</span>
                            </td>
                            <td class="nx-td-actions">
                                <button type="button" wire:click="edit({{ $movement->id }})"
                                        class="nx-action-btn nx-action-edit" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button type="button" wire:click="delete({{ $movement->id }})"
                                        wire:confirm="Tem certeza que deseja excluir esta movimentação?"
                                        class="nx-action-btn nx-action-delete" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="nx-empty-state" style="padding:48px 20px;">
                                    <div style="width:64px;height:64px;border-radius:50%;background:#F1F5F9;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5">
                                            <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                                        </svg>
                                    </div>
                                    <p style="font-size:14px;font-weight:600;color:#475569;margin:0 0 4px;">Nenhuma movimentação encontrada</p>
                                    <p style="font-size:13px;color:#94A3B8;margin:0;">Ajuste os filtros ou registre uma nova movimentação.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginação --}}
    <div style="margin-top:-4px;">
        {{ $movements->links() }}
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL DE FORMULÁRIO
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="nx-mov-modal-wrap" wire:click.self="closeModal">
            <div class="nx-mov-modal">

                {{-- Header --}}
                <div class="nx-mov-modal-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="nx-mov-modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                        </div>
                        <div>
                            <p class="nx-mov-modal-title">{{ $editingId ? 'Editar Movimentação' : 'Nova Movimentação' }}</p>
                            <p class="nx-mov-modal-subtitle">Preencha os dados da movimentação de estoque</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit.prevent="save">
                    <div class="nx-mov-modal-body">

                        {{-- Produto --}}
                        <div class="nx-field">
                            <label>Produto <span style="color:#EF4444">*</span></label>
                            <select wire:model="product_id" required>
                                <option value="">Selecione um produto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} — Estoque: {{ $product->stock ?? 0 }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <small style="color:#EF4444">{{ $message }}</small> @enderror
                        </div>

                        {{-- Tipo + Quantidade --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="nx-field">
                                <label>Tipo <span style="color:#EF4444">*</span></label>
                                <select wire:model.live="type" required>
                                    <option value="input">Entrada</option>
                                    <option value="output">Saída</option>
                                    <option value="adjustment">Ajuste</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                                @error('type') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Quantidade <span style="color:#EF4444">*</span></label>
                                <input type="number" wire:model="quantity" step="0.001" min="0.001" required placeholder="0.000">
                                @error('quantity') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Origem + Custo Unitário --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="nx-field">
                                <label>Origem <span style="color:#EF4444">*</span></label>
                                <input type="text" wire:model="origin" required placeholder="Ex: Compra #123, Venda #45">
                                @error('origin') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Custo Unitário</label>
                                <input type="number" wire:model="unit_cost" step="0.01" min="0" placeholder="0,00">
                                @error('unit_cost') <small style="color:#EF4444">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Observação --}}
                        <div class="nx-field">
                            <label>
                                Observação {{ $type === 'adjustment' ? '(obrigatória para ajustes)' : '' }}
                                @if($type === 'adjustment') <span style="color:#EF4444">*</span> @endif
                            </label>
                            <textarea wire:model="observation" rows="3"
                                      {{ $type === 'adjustment' ? 'required' : '' }}
                                      placeholder="Descreva o motivo desta movimentação..."></textarea>
                            @error('observation') <small style="color:#EF4444">{{ $message }}</small> @enderror
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="nx-mov-modal-footer">
                        <button type="button" wire:click="closeModal" class="nx-btn nx-btn-outline">
                            Cancelar
                        </button>
                        <button type="submit" class="nx-btn nx-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            {{ $editingId ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
