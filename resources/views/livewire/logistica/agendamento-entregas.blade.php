<div class="nx-ds-page">

    {{-- ══════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'transporte') }}" class="nx-breadcrumb-link" wire:navigate>Transporte</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Agendamento de Entregas</span>
            </nav>
            <h1 class="nx-page-title">Agendamento de Entregas</h1>
            <p class="nx-page-subtitle">Organize e controle as entregas por data, janela horária e veículo</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Agendamento
            </button>
        </div>
    </div>

    {{-- ── FLASH ── --}}
    @session('message')
        <div class="nx-ds-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════ --}}
    <div class="nx-ds-kpis">
        <div class="nx-kpi-card" style="border-top:3px solid #6366F1">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #3B82F6">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Agendados</p>
                    <p class="nx-kpi-card-value" style="color:#3B82F6">{{ $stats['agendado'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.08);color:#3B82F6;border-color:rgba(59,130,246,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #0EA5E9">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Em Rota</p>
                    <p class="nx-kpi-card-value" style="color:#0EA5E9">{{ $stats['em_rota'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(14,165,233,0.08);color:#0EA5E9;border-color:rgba(14,165,233,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #10B981">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Entregues</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $stats['entregue'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #EF4444">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Não Entregues</p>
                    <p class="nx-kpi-card-value" style="color:#EF4444">{{ $stats['nao_entregue'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.08);color:#EF4444;border-color:rgba(239,68,68,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #F97316">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Reagendados</p>
                    <p class="nx-kpi-card-value" style="color:#F97316">{{ $stats['reagendado'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(249,115,22,0.08);color:#F97316;border-color:rgba(249,115,22,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.24"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════ --}}
    <div class="nx-ds-filters">
        <div class="nx-search-wrap" style="flex:1;min-width:200px;max-width:320px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar cliente, endereço, nº..." class="nx-search">
        </div>
        <input type="date" wire:model.live="filterDate" class="nx-filter-select" style="max-width:160px;" title="Filtrar por data">
        <select wire:model.live="filterStatus" class="nx-filter-select">
            <option value="">Todos os Status</option>
            @foreach($statuses as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPriority" class="nx-filter-select">
            <option value="">Todas Prioridades</option>
            @foreach($priorities as $p)
                <option value="{{ $p->value }}">{{ $p->label() }}</option>
            @endforeach
        </select>
        @if($search || $filterStatus || $filterDate || $filterPriority)
            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm" style="color:#EF4444;border-color:rgba(239,68,68,0.3);white-space:nowrap;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Limpar
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         TABELA
    ══════════════════════════════════════════ --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Agendamento</th>
                        <th>Cliente</th>
                        <th>Endereço</th>
                        <th>Data</th>
                        <th>Janela</th>
                        <th>Veículo</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr wire:key="ds-{{ $schedule->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;font-size:13px;">
                                    {{ $schedule->schedule_number }}
                                </strong>
                                @if($schedule->order)
                                    <div style="font-size:11px;color:#94A3B8;margin-top:2px;">{{ $schedule->order->order_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:600;color:#1E293B;">{{ $schedule->client_name }}</div>
                            </td>
                            <td>
                                <div style="font-size:12px;color:#475569;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $schedule->delivery_address }}">
                                    {{ $schedule->delivery_address }}
                                </div>
                            </td>
                            <td style="white-space:nowrap;">
                                <div style="font-size:13px;font-weight:600;color:#1E293B;">
                                    {{ $schedule->delivery_date?->format('d/m/Y') }}
                                </div>
                                @if($schedule->delivery_date?->isToday())
                                    <div style="font-size:10px;color:#10B981;font-weight:700;">Hoje</div>
                                @elseif($schedule->delivery_date?->isPast())
                                    <div style="font-size:10px;color:#EF4444;font-weight:700;">Atrasado</div>
                                @endif
                            </td>
                            <td>
                                @if($schedule->timeWindow)
                                    <span style="font-size:12px;color:#475569;">
                                        {{ substr($schedule->timeWindow->start_time, 0, 5) }}–{{ substr($schedule->timeWindow->end_time, 0, 5) }}
                                    </span>
                                    <div style="font-size:11px;color:#94A3B8;">{{ $schedule->timeWindow->name }}</div>
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($schedule->vehicle)
                                    <div style="font-size:12px;color:#475569;">{{ $schedule->vehicle->display_name }}</div>
                                    <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $schedule->vehicle->plate }}</div>
                                @elseif($schedule->driver_name)
                                    <div style="font-size:12px;color:#475569;">{{ $schedule->driver_name }}</div>
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="nx-ds-priority nx-ds-priority--{{ $schedule->priority?->value ?? 'normal' }}">
                                    {{ $schedule->priority?->label() ?? 'Normal' }}
                                </span>
                            </td>
                            <td>
                                <span class="nx-ds-badge {{ $schedule->status?->badgeClass() }}">
                                    {{ $schedule->status?->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="nx-table-actions">
                                    <button type="button" wire:click="openDetail({{ $schedule->id }})" class="nx-table-btn" title="Ver detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $schedule->id }})" class="nx-table-btn" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button type="button" wire:click="openReschedule({{ $schedule->id }})" class="nx-table-btn nx-table-btn--orange" title="Reagendar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.24"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="nx-ds-empty">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <p style="font-weight:600;color:#64748B;margin-top:12px;">Nenhum agendamento encontrado</p>
                                    <p style="font-size:13px;color:#94A3B8;margin-top:4px;">Clique em "Novo Agendamento" para começar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         MODAL — CRIAR / EDITAR
    ══════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-ds-modal-wrap" wire:click.self="closeModal">
        <div class="nx-ds-modal">

            {{-- Header --}}
            <div class="nx-ds-modal-header">
                <div>
                    <h2 class="nx-ds-modal-title">
                        {{ $editingId ? 'Editar Agendamento' : 'Novo Agendamento' }}
                    </h2>
                    <p style="font-size:13px;color:#64748B;margin-top:2px;">Preencha as informações de entrega</p>
                </div>
                <button type="button" wire:click="closeModal" class="nx-ds-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="nx-ds-modal-body">

                {{-- Pedido de Venda --}}
                <div class="nx-so-section-title">Vincular Pedido (Opcional)</div>
                <div class="nx-so-grid-2" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Pedido de Venda</label>
                        <select wire:model.live="order_id">
                            <option value="">— Sem vínculo —</option>
                            @foreach($salesOrders as $so)
                                <option value="{{ $so['id'] }}">{{ $so['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Cliente e Endereço --}}
                <div class="nx-so-section-title">Dados de Entrega</div>
                <div class="nx-so-grid-2" style="margin-bottom:14px;">
                    <div class="nx-field">
                        <label>Cliente *</label>
                        <input type="text" wire:model="client_name" placeholder="Nome do cliente">
                        @error('client_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Data de Entrega *</label>
                        <input type="date" wire:model="delivery_date" min="{{ now()->format('Y-m-d') }}">
                        @error('delivery_date') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="nx-field" style="margin-bottom:14px;">
                    <label>Endereço de Entrega *</label>
                    <input type="text" wire:model="delivery_address" placeholder="Rua, número, bairro, cidade, estado">
                    @error('delivery_address') <span class="nx-field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Janela e Veículo --}}
                <div class="nx-so-section-title">Logística</div>
                <div class="nx-so-grid-2" style="margin-bottom:14px;">
                    <div class="nx-field">
                        <label>Janela de Entrega</label>
                        <select wire:model="time_window_id">
                            <option value="">— Sem janela —</option>
                            @foreach($timeWindows as $tw)
                                <option value="{{ $tw->id }}">{{ $tw->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="nx-field">
                        <label>Veículo</label>
                        <select wire:model="vehicle_id">
                            <option value="">— Não definido —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->display_name }} — {{ $v->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="nx-so-grid-3" style="margin-bottom:14px;">
                    <div class="nx-field">
                        <label>Motorista / Responsável</label>
                        <input type="text" wire:model="driver_name" placeholder="Nome do motorista">
                    </div>
                    <div class="nx-field">
                        <label>Peso (kg)</label>
                        <input type="number" wire:model="weight_kg" placeholder="0.000" step="0.001" min="0">
                    </div>
                    <div class="nx-field">
                        <label>Volume (m³)</label>
                        <input type="number" wire:model="volume_m3" placeholder="0.000" step="0.001" min="0">
                    </div>
                </div>

                {{-- Status e Prioridade --}}
                <div class="nx-so-section-title">Classificação</div>
                <div class="nx-so-grid-2" style="margin-bottom:14px;">
                    <div class="nx-field">
                        <label>Prioridade *</label>
                        <select wire:model="priority">
                            <option value="">Selecione...</option>
                            @foreach($priorities as $p)
                                <option value="{{ $p->value }}">{{ $p->label() }}</option>
                            @endforeach
                        </select>
                        @error('priority') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Status *</label>
                        <select wire:model="status">
                            <option value="">Selecione...</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="nx-field">
                    <label>Observações</label>
                    <textarea wire:model="notes" rows="3" placeholder="Observações adicionais sobre a entrega..."></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="nx-ds-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </span>
                    {{ $editingId ? 'Salvar Alterações' : 'Criar Agendamento' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         MODAL — DETALHES
    ══════════════════════════════════════════ --}}
    @if($showDetail && $viewingSchedule)
    <div class="nx-ds-modal-wrap" wire:click.self="closeDetail">
        <div class="nx-ds-modal" style="max-width:680px;">
            <div class="nx-ds-modal-header">
                <div>
                    <h2 class="nx-ds-modal-title">{{ $viewingSchedule->schedule_number }}</h2>
                    <span class="nx-ds-badge {{ $viewingSchedule->status?->badgeClass() }}" style="margin-top:4px;display:inline-block;">
                        {{ $viewingSchedule->status?->label() }}
                    </span>
                </div>
                <button type="button" wire:click="closeDetail" class="nx-ds-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-ds-modal-body">
                <div class="nx-ds-detail-grid">
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Cliente</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->client_name }}</span>
                    </div>
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Data de Entrega</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->delivery_date?->format('d/m/Y') }}</span>
                    </div>
                    <div class="nx-ds-detail-item" style="grid-column:span 2;">
                        <span class="nx-ds-detail-label">Endereço</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->delivery_address }}</span>
                    </div>
                    @if($viewingSchedule->timeWindow)
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Janela</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->timeWindow->display_name }}</span>
                    </div>
                    @endif
                    @if($viewingSchedule->vehicle)
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Veículo</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->vehicle->display_name }} — {{ $viewingSchedule->vehicle->plate }}</span>
                    </div>
                    @endif
                    @if($viewingSchedule->driver_name)
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Motorista</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->driver_name }}</span>
                    </div>
                    @endif
                    @if($viewingSchedule->weight_kg || $viewingSchedule->volume_m3)
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Carga</span>
                        <span class="nx-ds-detail-value">
                            @if($viewingSchedule->weight_kg) {{ number_format($viewingSchedule->weight_kg, 2, ',', '.') }} kg @endif
                            @if($viewingSchedule->volume_m3) / {{ number_format($viewingSchedule->volume_m3, 3, ',', '.') }} m³ @endif
                        </span>
                    </div>
                    @endif
                    <div class="nx-ds-detail-item">
                        <span class="nx-ds-detail-label">Prioridade</span>
                        <span class="nx-ds-priority nx-ds-priority--{{ $viewingSchedule->priority?->value ?? 'normal' }}">
                            {{ $viewingSchedule->priority?->label() ?? 'Normal' }}
                        </span>
                    </div>
                    @if($viewingSchedule->notes)
                    <div class="nx-ds-detail-item" style="grid-column:span 2;">
                        <span class="nx-ds-detail-label">Observações</span>
                        <span class="nx-ds-detail-value">{{ $viewingSchedule->notes }}</span>
                    </div>
                    @endif
                    @if($viewingSchedule->reschedule_reason)
                    <div class="nx-ds-detail-item" style="grid-column:span 2;">
                        <span class="nx-ds-detail-label">Motivo do Reagendamento</span>
                        <span class="nx-ds-detail-value" style="color:#F97316;">{{ $viewingSchedule->reschedule_reason }}</span>
                    </div>
                    @endif
                </div>

                {{-- Ações de status rápido --}}
                <div style="margin-top:20px;padding-top:20px;border-top:1px solid #F1F5F9;">
                    <p style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Alterar Status</p>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($statuses as $s)
                            @if($s->value !== $viewingSchedule->status?->value)
                            <button type="button"
                                wire:click="changeStatus({{ $viewingSchedule->id }}, '{{ $s->value }}')"
                                class="nx-ds-status-btn"
                                style="border-color:{{ $s->color() }};color:{{ $s->color() }};">
                                {{ $s->label() }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="nx-ds-modal-footer">
                <button type="button" wire:click="delete({{ $viewingSchedule->id }})"
                    wire:confirm="Tem certeza que deseja excluir este agendamento?"
                    class="nx-btn nx-btn-ghost" style="color:#EF4444;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Excluir
                </button>
                <div style="display:flex;gap:8px;">
                    <button type="button" wire:click="openReschedule({{ $viewingSchedule->id }})" class="nx-btn nx-btn-outline" style="color:#F97316;border-color:rgba(249,115,22,0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.24"/></svg>
                        Reagendar
                    </button>
                    <button type="button" wire:click="edit({{ $viewingSchedule->id }})" class="nx-btn nx-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         MODAL — REAGENDAMENTO
    ══════════════════════════════════════════ --}}
    @if($showReschedule)
    <div class="nx-ds-modal-wrap" wire:click.self="closeReschedule">
        <div class="nx-ds-modal" style="max-width:520px;">
            <div class="nx-ds-modal-header">
                <div>
                    <h2 class="nx-ds-modal-title">Reagendar Entrega</h2>
                    <p style="font-size:13px;color:#64748B;margin-top:2px;">Informe a nova data e o motivo do reagendamento</p>
                </div>
                <button type="button" wire:click="closeReschedule" class="nx-ds-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-ds-modal-body">
                <div class="nx-so-grid-2" style="margin-bottom:14px;">
                    <div class="nx-field">
                        <label>Nova Data de Entrega *</label>
                        <input type="date" wire:model="reschedule_date" min="{{ now()->format('Y-m-d') }}">
                        @error('reschedule_date') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Nova Janela de Entrega</label>
                        <select wire:model="reschedule_window">
                            <option value="">— Manter atual —</option>
                            @foreach($timeWindows as $tw)
                                <option value="{{ $tw->id }}">{{ $tw->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="nx-field">
                    <label>Motivo do Reagendamento *</label>
                    <textarea wire:model="reschedule_reason" rows="3" placeholder="Descreva o motivo do reagendamento..."></textarea>
                    @error('reschedule_reason') <span class="nx-field-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="nx-ds-modal-footer">
                <button type="button" wire:click="closeReschedule" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="confirmReschedule" wire:loading.attr="disabled" class="nx-btn nx-btn-primary" style="background:#F97316;border-color:#F97316;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.24"/></svg>
                    Confirmar Reagendamento
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

