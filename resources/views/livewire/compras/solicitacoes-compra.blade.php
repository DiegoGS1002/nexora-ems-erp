<div class="nx-so-page">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'compras') }}" class="nx-breadcrumb-link" wire:navigate>Compras</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Solicitações de Compra</span>
            </nav>
            <h1 class="nx-page-title">Solicitações de Compra</h1>
            <p class="nx-page-subtitle">Solicitações internas de aquisição de materiais e serviços</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Solicitação
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
                    <p class="nx-kpi-card-title">Total de Solicitações</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
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
                    <p class="nx-kpi-card-title">Aprovadas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['aprovadas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #EF4444">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Rejeitadas</p>
                    <p class="nx-kpi-card-value" style="color:#EF4444">{{ $stats['rejeitadas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.08);color:#EF4444;border-color:rgba(239,68,68,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #0EA5E9">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Valor Est. Total</p>
                    <p class="nx-kpi-card-value" style="color:#0EA5E9;font-size:15px;">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar n° solicitação, título ou solicitante..." class="nx-search">
        </div>
        <select wire:model.live="filterStatus" class="nx-filter-select">
            <option value="">Todos os Status</option>
            @foreach($statuses as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPriority" class="nx-filter-select">
            <option value="">Todas as Prioridades</option>
            @foreach($priorities as $p)
                <option value="{{ $p->value }}">{{ $p->label() }}</option>
            @endforeach
        </select>
        @if($search || $filterStatus || $filterPriority)
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
                        <th>Solicitação</th>
                        <th>Título</th>
                        <th>Solicitante</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th class="nx-th-right">Itens</th>
                        <th class="nx-th-right">Valor Est.</th>
                        <th>Necessidade</th>
                        <th>Data</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $req)
                        <tr wire:key="req-{{ $req->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;">{{ $req->number }}</strong>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:600;color:#1E293B;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $req->title }}">
                                    {{ $req->title }}
                                </div>
                                @if($req->department)
                                    <div style="font-size:11px;color:#94A3B8;">{{ $req->department }}</div>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#475569;">{{ $req->requester?->name ?? '—' }}</td>
                            <td>
                                <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:5px;{{ $req->priority->badgeStyle() }}">
                                    {{ $req->priority->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="nx-so-badge {{ $req->status->badgeClass() }}">
                                    {{ $req->status->label() }}
                                </span>
                            </td>
                            <td class="nx-td-right" style="font-size:13px;color:#475569;">{{ $req->items_count ?? 0 }}</td>
                            <td class="nx-td-right" style="font-size:13px;font-weight:600;color:#0F172A;">
                                R$ {{ number_format($req->items->sum(fn($i) => (float)$i->quantity * (float)$i->estimated_price), 2, ',', '.') }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                @if($req->needed_by)
                                    <span style="{{ $req->needed_by->isPast() && !in_array($req->status->value, ['aprovada','convertida','cancelada','rejeitada']) ? 'color:#EF4444;font-weight:600;' : '' }}">
                                        {{ $req->needed_by->format('d/m/Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size:12px;color:#64748B;">{{ $req->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" wire:click="openDetail({{ $req->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Ver detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if($req->canEdit())
                                        <button type="button" wire:click="edit({{ $req->id }})"
                                            class="nx-btn nx-btn-ghost nx-btn-sm" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                @include('partials.empty-state', [
                                    'title'       => 'Nenhuma solicitação encontrada',
                                    'description' => 'Crie uma nova solicitação clicando no botão acima.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requisitions->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $requisitions->links() }}
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
                        {{ $editingId ? 'Editar Solicitação' : 'Nova Solicitação de Compra' }}
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                        Preencha os dados e adicione os itens necessários
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
                        ['key'=>'geral', 'label'=>'Geral',        'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                        ['key'=>'itens', 'label'=>'Itens',        'icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
                        ['key'=>'obs',   'label'=>'Justificativa', 'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
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
                        Identificação
                    </h3>
                    <div class="nx-field">
                        <label>Título / Descrição <span style="color:#EF4444">*</span></label>
                        <input type="text" wire:model="title" placeholder="Ex: Materiais de escritório para setor administrativo">
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
                            <label>Prioridade</label>
                            <select wire:model="priority">
                                @foreach($priorities as $p)
                                    <option value="{{ $p->value }}" @selected($priority === $p->value)>{{ $p->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Data de Necessidade</label>
                            <input type="date" wire:model="needed_by">
                        </div>
                    </div>
                    <div class="nx-so-grid-2">
                        <div class="nx-field">
                            <label>Solicitante</label>
                            <select wire:model="requester_id">
                                <option value="">— Selecione —</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @selected($requester_id == $u->id)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-field">
                            <label>Departamento / Setor</label>
                            <input type="text" wire:model="department" placeholder="Ex: Administrativo, TI, Produção...">
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── ABA: ITENS ───────────────────────────────────── --}}
                @if($activeTab === 'itens')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        Itens Solicitados
                    </h3>

                    {{-- Product search --}}
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

                    @error('reqItems')<div class="alert-error" style="margin-bottom:12px;">{{ $message }}</div>@enderror

                    @forelse($reqItems as $i => $item)
                        <div class="nx-so-item-card" wire:key="req-item-{{ $i }}">
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
                            <div class="nx-so-item-fields" style="grid-template-columns:2fr 1fr 1fr 1fr 1fr;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Descrição <span style="color:#EF4444">*</span></label>
                                    <input type="text" wire:model.blur="reqItems.{{ $i }}.description" placeholder="Descrição do item">
                                    @error("reqItems.$i.description")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Unidade</label>
                                    <input type="text" wire:model="reqItems.{{ $i }}.unit" placeholder="UN">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Quantidade <span style="color:#EF4444">*</span></label>
                                    <input type="number" step="0.001" min="0.001" wire:model.blur="reqItems.{{ $i }}.quantity">
                                    @error("reqItems.$i.quantity")<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Preço Est. (R$)</label>
                                    <input type="number" step="0.01" min="0" wire:model.blur="reqItems.{{ $i }}.estimated_price">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Total Est.</label>
                                    <div class="nx-so-item-total">
                                        R$ {{ number_format((float)($item['quantity'] ?? 0) * (float)($item['estimated_price'] ?? 0), 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Centro de Custo</label>
                                    <input type="text" wire:model="reqItems.{{ $i }}.cost_center" placeholder="Ex: ADM-001">
                                </div>
                                <div class="nx-field" style="margin-bottom:0;">
                                    <label>Observação do Item</label>
                                    <input type="text" wire:model="reqItems.{{ $i }}.notes" placeholder="Especificações, marca preferida...">
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

                    @if(count($reqItems))
                        <div class="nx-so-items-summary">
                            <span>{{ count($reqItems) }} {{ count($reqItems) === 1 ? 'item' : 'itens' }}</span>
                            <span style="font-weight:700;color:#0F172A;">Valor Est. Total: R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ── ABA: JUSTIFICATIVA ───────────────────────────── --}}
                @if($activeTab === 'obs')
                <div class="nx-so-tab-content">
                    <h3 class="nx-so-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Justificativa e Observações
                    </h3>
                    <div class="nx-field">
                        <label>Justificativa da Compra</label>
                        <textarea wire:model="justification" rows="4"
                            placeholder="Descreva o motivo da solicitação, urgência, impacto para a operação..."></textarea>
                    </div>
                    <div class="nx-field">
                        <label>Observações Internas</label>
                        <textarea wire:model="notes" rows="3" placeholder="Informações adicionais..."></textarea>
                    </div>
                </div>
                @endif

            </form>

            {{-- Modal Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:12px;color:#94A3B8;">
                        {{ count($reqItems) }} {{ count($reqItems) === 1 ? 'item' : 'itens' }}
                        · R$ {{ number_format($subtotal, 2, ',', '.') }}
                    </span>
                    <button type="button" wire:click="save" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ $editingId ? 'Salvar Alterações' : 'Criar Solicitação' }}
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
    @if($showDetail && $viewingRequisition)
    <div class="nx-so-modal-wrap" wire:click.self="closeDetail">
        <div class="nx-so-modal" style="max-width:800px;">

            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                        <strong style="font-family:monospace;font-size:16px;color:#6366F1;">{{ $viewingRequisition->number }}</strong>
                        <span class="nx-so-badge {{ $viewingRequisition->status->badgeClass() }}">{{ $viewingRequisition->status->label() }}</span>
                        <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:5px;{{ $viewingRequisition->priority->badgeStyle() }}">
                            {{ $viewingRequisition->priority->label() }}
                        </span>
                    </div>
                    <p style="font-size:14px;font-weight:600;color:#0F172A;margin:0 0 2px;">{{ $viewingRequisition->title }}</p>
                    @if($viewingRequisition->department)
                        <p style="font-size:12px;color:#94A3B8;margin:0;">{{ $viewingRequisition->department }}</p>
                    @endif
                </div>
                <button type="button" wire:click="closeDetail"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-so-modal-body">

                {{-- Info grid --}}
                <div class="nx-so-detail-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Solicitante</span>
                        <strong>{{ $viewingRequisition->requester?->name ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Data de Necessidade</span>
                        <strong>{{ $viewingRequisition->needed_by?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Criado em</span>
                        <strong>{{ $viewingRequisition->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if($viewingRequisition->approved_by)
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Aprovado por</span>
                        <strong>{{ $viewingRequisition->approver?->name ?? '—' }}</strong>
                    </div>
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Aprovado em</span>
                        <strong>{{ $viewingRequisition->approved_at?->format('d/m/Y H:i') ?? '—' }}</strong>
                    </div>
                    @endif
                    @if($viewingRequisition->purchaseOrder)
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Pedido Gerado</span>
                        <strong style="color:#6366F1;font-family:monospace;">{{ $viewingRequisition->purchaseOrder->order_number }}</strong>
                    </div>
                    @endif
                    @if($viewingRequisition->cotacao)
                    <div class="nx-so-detail-item">
                        <span class="nx-so-detail-label">Cotação Gerada</span>
                        <strong style="color:#6366F1;font-family:monospace;">{{ $viewingRequisition->cotacao->number }}</strong>
                    </div>
                    @endif
                </div>

                {{-- Rejection reason --}}
                @if($viewingRequisition->rejection_reason)
                    <div style="margin-bottom:16px;padding:12px 14px;background:#FEF2F2;border-radius:8px;border-left:3px solid #EF4444;">
                        <strong style="font-size:12px;color:#991B1B;">Motivo da Rejeição:</strong>
                        <p style="font-size:13px;color:#7F1D1D;margin:4px 0 0;">{{ $viewingRequisition->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Items table --}}
                <h4 style="font-size:13px;font-weight:700;color:#0F172A;margin-bottom:10px;">Itens Solicitados</h4>
                <div class="nx-table-wrap" style="margin-bottom:16px;">
                    <table class="nx-table" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descrição</th>
                                <th>SKU</th>
                                <th>Unid.</th>
                                <th class="nx-th-right">Qtd</th>
                                <th class="nx-th-right">Preço Est.</th>
                                <th class="nx-th-right">Total Est.</th>
                                <th>C. Custo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingRequisition->items as $i => $item)
                            <tr>
                                <td style="color:#94A3B8;">{{ $i + 1 }}</td>
                                <td>
                                    <div style="font-weight:600;color:#1E293B;">{{ $item->description }}</div>
                                    @if($item->notes)<div style="font-size:10px;color:#94A3B8;">{{ $item->notes }}</div>@endif
                                </td>
                                <td style="font-family:monospace;color:#64748B;">{{ $item->sku ?? '—' }}</td>
                                <td>{{ $item->unit }}</td>
                                <td class="nx-td-right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                                <td class="nx-td-right">R$ {{ number_format($item->estimated_price, 2, ',', '.') }}</td>
                                <td class="nx-td-right" style="font-weight:700;">R$ {{ number_format($item->getTotal(), 2, ',', '.') }}</td>
                                <td style="color:#64748B;">{{ $item->cost_center ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#F8FAFC;">
                                <td colspan="6" style="text-align:right;font-size:12px;font-weight:700;color:#475569;padding:10px 12px;">Valor Total Estimado</td>
                                <td class="nx-td-right" style="font-size:14px;font-weight:700;color:#0F172A;padding:10px 12px;">
                                    R$ {{ number_format($viewingRequisition->getTotalEstimado(), 2, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Justification / Notes --}}
                @if($viewingRequisition->justification)
                    <div style="padding:12px 14px;background:#F8FAFC;border-radius:8px;border-left:3px solid #CBD5E1;margin-bottom:10px;">
                        <strong style="font-size:12px;color:#64748B;">Justificativa:</strong>
                        <p style="font-size:13px;color:#475569;margin:4px 0 0;">{{ $viewingRequisition->justification }}</p>
                    </div>
                @endif
                @if($viewingRequisition->notes)
                    <div style="padding:12px 14px;background:#F8FAFC;border-radius:8px;border-left:3px solid #CBD5E1;">
                        <strong style="font-size:12px;color:#64748B;">Observações:</strong>
                        <p style="font-size:13px;color:#475569;margin:4px 0 0;">{{ $viewingRequisition->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Detail Footer --}}
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-ghost">Fechar</button>
                <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">

                    {{-- Submit for approval --}}
                    @if($viewingRequisition->status === \App\Enums\SolicitacaoCompraStatus::Rascunho)
                        <button type="button" wire:click="submitForApproval({{ $viewingRequisition->id }})"
                            class="nx-btn" style="background:#6366F1;color:#fff;border-color:#6366F1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Enviar para Aprovação
                        </button>
                    @endif

                    {{-- Approve / Reject --}}
                    @if($viewingRequisition->status === \App\Enums\SolicitacaoCompraStatus::AguardandoAprovacao)
                        <button type="button" wire:click="approve({{ $viewingRequisition->id }})"
                            class="nx-btn" style="background:#10B981;color:#fff;border-color:#10B981;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Aprovar
                        </button>
                        <button type="button" wire:click="openRejectModal"
                            class="nx-btn" style="background:#FEF2F2;color:#DC2626;border-color:rgba(239,68,68,0.3);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Rejeitar
                        </button>
                    @endif

                    {{-- Convert --}}
                    @if($viewingRequisition->status === \App\Enums\SolicitacaoCompraStatus::Aprovada)
                        <button type="button"
                            wire:click="convertToCotacao({{ $viewingRequisition->id }})"
                            wire:confirm="Deseja criar uma Cotação a partir desta solicitação?"
                            class="nx-btn nx-btn-outline"
                            style="border-color:#6366F1;color:#6366F1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            Gerar Cotação
                        </button>
                        <button type="button"
                            wire:click="convertToPurchaseOrder({{ $viewingRequisition->id }})"
                            wire:confirm="Deseja gerar um Pedido de Compra diretamente desta solicitação?"
                            class="nx-btn nx-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            Gerar Pedido de Compra
                        </button>
                    @endif

                    {{-- Edit / Cancel --}}
                    @if($viewingRequisition->canEdit())
                        <button type="button" wire:click="edit({{ $viewingRequisition->id }})" class="nx-btn nx-btn-outline">
                            Editar
                        </button>
                    @endif
                    @if(!in_array($viewingRequisition->status->value, ['cancelada','convertida','rejeitada']))
                        <button type="button"
                            wire:click="cancel({{ $viewingRequisition->id }})"
                            wire:confirm="Tem certeza que deseja cancelar esta solicitação?"
                            class="nx-btn" style="background:#FEF2F2;color:#DC2626;border-color:rgba(239,68,68,0.3);">
                            Cancelar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         MODAL — REJEIÇÃO
    ══════════════════════════════════════════════════════════════ --}}
    @if($showRejectModal)
    <div class="nx-so-modal-wrap" style="z-index:9999;" wire:click.self="closeRejectModal">
        <div class="nx-so-modal" style="max-width:500px;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 0;flex-shrink:0;">
                <div>
                    <h2 style="font-size:16px;font-weight:700;color:#0F172A;margin:0;">Rejeitar Solicitação</h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">Informe o motivo da rejeição</p>
                </div>
                <button type="button" wire:click="closeRejectModal"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-so-modal-body">
                <div class="nx-field">
                    <label>Motivo da Rejeição <span style="color:#EF4444">*</span></label>
                    <textarea wire:model="rejection_reason" rows="4" placeholder="Descreva o motivo da rejeição..."></textarea>
                    @error('rejection_reason')<span class="nx-field-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="nx-so-modal-footer">
                <button type="button" wire:click="closeRejectModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="reject" class="nx-btn" style="background:#EF4444;color:#fff;border-color:#EF4444;"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="reject">Confirmar Rejeição</span>
                    <span wire:loading wire:target="reject">Rejeitando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

