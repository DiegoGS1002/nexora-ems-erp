<div class="nx-jornada-page">

    {{-- ══════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'rh') }}" class="nx-breadcrumb-link">Recursos Humanos</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Jornada de Trabalho</span>
            </nav>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <h1 class="nx-page-title">Jornada de Trabalho</h1>
                <span class="nx-jornada-live-badge">
                    <span class="nx-jornada-live-dot"></span>
                    Ao Vivo
                </span>
            </div>
            <p class="nx-page-subtitle">Monitoramento de assiduidade e controle de ponto em tempo real</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openShiftModal()" class="nx-btn nx-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 19.07a10 10 0 0 1 0-14.14"/></svg>
                Gerenciar Turnos
            </button>
            <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Registrar Ponto
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
    <div class="nx-jornada-kpis">

        {{-- Presentes --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Presentes Hoje</p>
                    <p class="nx-kpi-card-value" style="color:#059669;font-size:28px;">
                        {{ $this->kpis['present'] }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">
                        de {{ $this->kpis['total_employees'] }} colaboradores
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="nx-kpi-card-progress">
                <div class="nx-kpi-card-progress-bar" style="width:{{ $this->kpis['total_employees'] > 0 ? round(($this->kpis['present']/$this->kpis['total_employees'])*100) : 0 }}%;background:#10B981;"></div>
            </div>
        </div>

        {{-- Em Pausa --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Em Pausa</p>
                    <p class="nx-kpi-card-value" style="color:#D97706;font-size:28px;">
                        {{ $this->kpis['on_break'] }}
                    </p>
                    <span class="nx-kpi-card-trend" style="color:#92400E;">Em intervalo agora</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B;border-color:rgba(245,158,11,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                </div>
            </div>
        </div>

        {{-- Ausentes --}}
        <div class="nx-kpi-card nx-kpi-card--alert">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Ausentes</p>
                    <p class="nx-kpi-card-value" style="color:#DC2626;font-size:28px;">
                        {{ $this->kpis['absent'] }}
                    </p>
                    <span class="nx-kpi-card-trend is-negative">Não registraram ponto</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
        </div>

        {{-- Banco de Horas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Banco de Horas (Hoje)</p>
                    <p class="nx-kpi-card-value" style="color:#7C3AED;font-size:28px;">
                        {{ $this->kpis['bank_hours'] }}
                    </p>
                    <span class="nx-kpi-card-trend" style="color:#5B21B6;">Horas excedentes acumuladas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(139,92,246,0.1);color:#8B5CF6;border-color:rgba(139,92,246,0.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         TOOLBAR — DATA + BUSCA + FILTROS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-jornada-toolbar">
        <div class="nx-jornada-toolbar-left">
            <div class="nx-jornada-date-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#64748B;flex-shrink:0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="date" wire:model.live="filterDate" class="nx-jornada-date-input">
            </div>
            <div class="nx-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" wire:model.live="search" placeholder="Buscar colaborador..." class="nx-search-input">
            </div>
            <select wire:model.live="filterStatus" class="nx-jornada-select">
                <option value="">Todos os status</option>
                @foreach($this->statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="nx-jornada-toolbar-right">
            <div class="nx-view-toggle">
                <button wire:click="$set('viewMode','grid')" class="nx-view-btn {{ $viewMode === 'grid' ? 'active' : '' }}" title="Visão em cards">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                </button>
                <button wire:click="$set('viewMode','list')" class="nx-view-btn {{ $viewMode === 'list' ? 'active' : '' }}" title="Visão em lista">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </button>
            </div>
            <span class="nx-jornada-toolbar-info">
                {{ count($this->presenceGrid) }} colaborador(es) exibido(s)
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         GRID DE PRESENÇA
    ══════════════════════════════════════════════════ --}}
    @if($viewMode === 'grid')
        @if(count($this->presenceGrid) > 0)
            <div class="nx-jornada-grid">
                @foreach($this->presenceGrid as $record)
                    @php
                        $statusMap = [
                            'active'    => ['dot' => '#10B981', 'bg' => 'rgba(16,185,129,0.08)', 'border' => 'rgba(16,185,129,0.2)'],
                            'break'     => ['dot' => '#F59E0B', 'bg' => 'rgba(245,158,11,0.08)', 'border' => 'rgba(245,158,11,0.2)'],
                            'absent'    => ['dot' => '#EF4444', 'bg' => 'rgba(239,68,68,0.06)', 'border' => 'rgba(239,68,68,0.12)'],
                            'completed' => ['dot' => '#6366F1', 'bg' => 'rgba(99,102,241,0.08)', 'border' => 'rgba(99,102,241,0.2)'],
                        ];
                        $s = $statusMap[$record->status->value] ?? $statusMap['absent'];
                        $initials = collect(explode(' ', $record->employee->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                    @endphp
                    <div class="nx-jornada-card" style="border-color:{{ $s['border'] }}">
                        <div class="nx-jornada-card-header">
                            <div class="nx-jornada-avatar-wrap">
                                @if($record->employee->photo)
                                    <img src="{{ asset('storage/'.$record->employee->photo) }}" alt="{{ $record->employee->name }}" class="nx-jornada-avatar-img">
                                @else
                                    <div class="nx-jornada-avatar">{{ $initials }}</div>
                                @endif
                                <span class="nx-jornada-status-dot" style="background:{{ $s['dot'] }};box-shadow:0 0 0 3px {{ $s['bg'] }}"></span>
                            </div>
                            <div class="nx-jornada-card-info">
                                <h4 class="nx-jornada-card-name">{{ $record->employee->name }}</h4>
                                <p class="nx-jornada-card-role">{{ $record->employee->role ?? $record->shift_name }}</p>
                            </div>
                            <span class="nx-jornada-status-badge" style="background:{{ $s['bg'] }};color:{{ $s['dot'] }};border-color:{{ $s['border'] }}">
                                {{ $record->status->label() }}
                            </span>
                        </div>

                        <div class="nx-jornada-card-body">
                            <div class="nx-jornada-time-row">
                                <div class="nx-jornada-time-item">
                                    <span class="nx-jornada-time-label">Entrada</span>
                                    <span class="nx-jornada-time-value" style="color:#059669;">{{ $record->clock_in_time }}</span>
                                </div>
                                <div class="nx-jornada-time-item">
                                    <span class="nx-jornada-time-label">Intervalo</span>
                                    <span class="nx-jornada-time-value" style="color:#D97706;">{{ $record->break_time }}</span>
                                </div>
                                <div class="nx-jornada-time-item">
                                    <span class="nx-jornada-time-label">Saída</span>
                                    <span class="nx-jornada-time-value" style="color:#DC2626;">{{ $record->clock_out_time }}</span>
                                </div>
                                <div class="nx-jornada-time-item">
                                    <span class="nx-jornada-time-label">Trabalhado</span>
                                    <span class="nx-jornada-time-value" style="color:#7C3AED;">{{ $record->worked_hours }}</span>
                                </div>
                            </div>

                            {{-- Timeline bar --}}
                            <div class="nx-jornada-timeline">
                                @if($record->clock_in_time !== '--:--')
                                    @php
                                        $startH = intval(explode(':', $record->clock_in_time)[0] ?? 8);
                                        $pct = min(100, max(0, (($startH - 6) / 14) * 100));
                                        $outPct = $record->clock_out_time !== '--:--'
                                            ? min(100, max(0, ((intval(explode(':', $record->clock_out_time)[0] ?? 18) - 6) / 14) * 100))
                                            : min(100, (((int)date('H') - 6) / 14) * 100);
                                    @endphp
                                    <div class="nx-jornada-timeline-active" style="left:{{ $pct }}%;width:{{ max(2, $outPct - $pct) }}%;background:{{ $s['dot'] }}"></div>
                                @endif
                                <div class="nx-jornada-timeline-now" style="left:{{ min(100, max(0, (((int)date('H') + (int)date('i')/60) - 6) / 14 * 100)) }}%"></div>
                            </div>
                            <div class="nx-jornada-timeline-labels">
                                <span>06h</span><span>10h</span><span>14h</span><span>18h</span><span>20h</span>
                            </div>
                        </div>

                        @if($record->record_id)
                            <div class="nx-jornada-card-actions">
                                <button wire:click="openEdit({{ $record->record_id }})" class="nx-jornada-action-btn" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Editar
                                </button>
                                <button wire:click="confirmDelete({{ $record->record_id }})" class="nx-jornada-action-btn nx-jornada-action-btn--danger" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Remover
                                </button>
                            </div>
                        @else
                            <div class="nx-jornada-card-actions">
                                <button wire:click="openCreate" class="nx-jornada-action-btn" title="Registrar ponto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Registrar Ponto
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="nx-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <p>Nenhum colaborador encontrado para esta data ou filtro.</p>
                <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary" style="margin-top:12px;">
                    Registrar Primeiro Ponto
                </button>
            </div>
        @endif

    {{-- ══════════════════════════════════════════════════
         LIST VIEW — Tabela detalhada
    ══════════════════════════════════════════════════ --}}
    @else
        <div class="nx-table-wrapper">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Turno</th>
                        <th>Status</th>
                        <th>Entrada</th>
                        <th>Intervalo</th>
                        <th>Saída</th>
                        <th>Horas</th>
                        <th style="text-align:right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->presenceGrid as $record)
                        @php
                            $dotColors = ['active'=>'#10B981','break'=>'#F59E0B','absent'=>'#EF4444','completed'=>'#6366F1'];
                            $dotColor = $dotColors[$record->status->value] ?? '#94A3B8';
                            $initials = collect(explode(' ', $record->employee->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                        @endphp
                        <tr>
                            <td>
                                <div class="nx-folha-emp-cell">
                                    @if($record->employee->photo)
                                        <img src="{{ asset('storage/'.$record->employee->photo) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="">
                                    @else
                                        <div class="nx-folha-avatar">{{ $initials }}</div>
                                    @endif
                                    <div>
                                        <p class="nx-folha-emp-name">{{ $record->employee->name }}</p>
                                        <p class="nx-folha-emp-code">{{ $record->employee->department ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="nx-folha-role">{{ $record->shift_name }}</span></td>
                            <td>
                                <span class="nx-jornada-status-badge" style="background:rgba(0,0,0,0.05);color:{{ $dotColor }};border-color:{{ $dotColor }}20">
                                    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:{{ $dotColor }};margin-right:5px;"></span>
                                    {{ $record->status->label() }}
                                </span>
                            </td>
                            <td><span class="nx-jornada-mono" style="color:#059669;">{{ $record->clock_in_time }}</span></td>
                            <td><span class="nx-jornada-mono" style="color:#D97706;">{{ $record->break_time }}</span></td>
                            <td><span class="nx-jornada-mono" style="color:#DC2626;">{{ $record->clock_out_time }}</span></td>
                            <td><span class="nx-jornada-mono" style="color:#7C3AED;font-weight:700;">{{ $record->worked_hours }}</span></td>
                            <td style="text-align:right;">
                                @if($record->record_id)
                                    <div style="display:flex;justify-content:flex-end;gap:6px;">
                                        <button wire:click="openEdit({{ $record->record_id }})" class="nx-btn-icon" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $record->record_id }})" class="nx-btn-icon nx-btn-icon--danger" title="Excluir">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="nx-folha-emp-code">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="nx-empty-state">Nenhum registro para exibir.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         SEÇÃO: GESTÃO DE TURNOS
    ══════════════════════════════════════════════════ --}}
    @if(count($this->allShifts) > 0)
    <div class="nx-jornada-section">
        <div class="nx-jornada-section-header">
            <div>
                <h3 class="nx-jornada-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Turnos Cadastrados
                </h3>
                <p style="font-size:12px;color:#94A3B8;margin:0;">Configuração de escalas e horários</p>
            </div>
            <button type="button" wire:click="openShiftModal()" class="nx-btn nx-btn-secondary" style="font-size:12.5px;padding:7px 14px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Turno
            </button>
        </div>
        <div class="nx-jornada-shifts-grid">
            @foreach($this->allShifts as $shift)
                <div class="nx-jornada-shift-card">
                    <div class="nx-jornada-shift-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="nx-jornada-shift-info">
                        <h4 class="nx-jornada-shift-name">{{ $shift->name }}</h4>
                        <p class="nx-jornada-shift-hours">{{ $shift->start_time }} – {{ $shift->end_time }}</p>
                        @if($shift->description)
                            <p class="nx-jornada-shift-desc">{{ $shift->description }}</p>
                        @endif
                        <span class="nx-jornada-shift-break">Intervalo: {{ $shift->break_duration }}min</span>
                    </div>
                    <div class="nx-jornada-shift-badge {{ $shift->is_active ? 'active' : 'inactive' }}">
                        {{ $shift->is_active ? 'Ativo' : 'Inativo' }}
                    </div>
                    <div class="nx-jornada-shift-actions">
                        <button wire:click="openShiftModal({{ $shift->id }})" class="nx-btn-icon" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button wire:click="deleteShift({{ $shift->id }})" class="nx-btn-icon nx-btn-icon--danger"
                            wire:confirm="Deseja remover este turno?" title="Excluir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MODAL — REGISTRAR / EDITAR PONTO
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="nx-modal-backdrop" wire:click.self="$set('showModal',false)">
            <div class="nx-modal nx-modal--lg" x-data x-trap="true">
                <div class="nx-modal-header">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="nx-modal-header-icon" style="background:rgba(139,92,246,0.1);color:#8B5CF6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <h3 class="nx-modal-title">{{ $isEditing ? 'Editar Registro de Ponto' : 'Registrar Ponto' }}</h3>
                            <p style="font-size:12px;color:#94A3B8;margin:0;">Preencha as informações de jornada</p>
                        </div>
                    </div>
                    <button wire:click="$set('showModal',false)" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div class="nx-form-grid-2">
                        <div class="nx-form-group nx-form-group--full">
                            <label class="nx-form-label">Colaborador <span class="nx-form-required">*</span></label>
                            <select wire:model="form.employee_id" class="nx-form-select">
                                <option value="">Selecione o colaborador...</option>
                                @foreach($this->employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            @error('form.employee_id')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="nx-form-group">
                            <label class="nx-form-label">Data <span class="nx-form-required">*</span></label>
                            <input type="date" wire:model="form.date" class="nx-form-input">
                            @error('form.date')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="nx-form-group">
                            <label class="nx-form-label">Turno</label>
                            <select wire:model="form.work_shift_id" class="nx-form-select">
                                <option value="">Sem turno definido</option>
                                @foreach($this->shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }}-{{ $shift->end_time }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="nx-form-group">
                            <label class="nx-form-label">Status <span class="nx-form-required">*</span></label>
                            <select wire:model="form.status" class="nx-form-select">
                                @foreach($this->statuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            @error('form.status')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="nx-jornada-time-grid">
                        <div class="nx-form-group">
                            <label class="nx-form-label">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#10B981;margin-right:5px;"></span>
                                Entrada
                            </label>
                            <input type="time" wire:model="form.clock_in" class="nx-form-input nx-form-input--time">
                            @error('form.clock_in')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#F59E0B;margin-right:5px;"></span>
                                Início Intervalo
                            </label>
                            <input type="time" wire:model="form.break_start" class="nx-form-input nx-form-input--time">
                            @error('form.break_start')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#F59E0B;margin-right:5px;"></span>
                                Fim Intervalo
                            </label>
                            <input type="time" wire:model="form.break_end" class="nx-form-input nx-form-input--time">
                            @error('form.break_end')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#EF4444;margin-right:5px;"></span>
                                Saída
                            </label>
                            <input type="time" wire:model="form.clock_out" class="nx-form-input nx-form-input--time">
                            @error('form.clock_out')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="nx-form-group" style="margin-top:8px;">
                        <label class="nx-form-label">Observação</label>
                        <textarea wire:model="form.observation" rows="2" placeholder="Ex: Atestado médico, trabalho remoto..." class="nx-form-textarea"></textarea>
                        @error('form.observation')<p class="nx-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="$set('showModal',false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="save" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ $isEditing ? 'Atualizar' : 'Registrar Ponto' }}
                        </span>
                        <span wire:loading wire:target="save">Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MODAL — CONFIRMAR EXCLUSÃO
    ══════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
        <div class="nx-modal-backdrop" wire:click.self="$set('showDeleteModal',false)">
            <div class="nx-modal nx-modal--sm">
                <div class="nx-modal-header">
                    <h3 class="nx-modal-title" style="color:#DC2626;">Excluir Registro</h3>
                    <button wire:click="$set('showDeleteModal',false)" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:42px;height:42px;border-radius:50%;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:600;color:#1E293B;margin:0 0 4px;">Confirmar exclusão</p>
                            <p style="font-size:13px;color:#64748B;margin:0;">Este registro de ponto será removido permanentemente. Esta ação não pode ser desfeita.</p>
                        </div>
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="$set('showDeleteModal',false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="deleteRecord" class="nx-btn" style="background:#EF4444;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                        Excluir Registro
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MODAL — GERENCIAR TURNOS
    ══════════════════════════════════════════════════ --}}
    @if($showShiftModal)
        <div class="nx-modal-backdrop" wire:click.self="$set('showShiftModal',false)">
            <div class="nx-modal nx-modal--md">
                <div class="nx-modal-header">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="nx-modal-header-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <h3 class="nx-modal-title">{{ $editingShiftId ? 'Editar Turno' : 'Novo Turno' }}</h3>
                    </div>
                    <button wire:click="$set('showShiftModal',false)" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div class="nx-form-grid-2">
                        <div class="nx-form-group nx-form-group--full">
                            <label class="nx-form-label">Nome do Turno <span class="nx-form-required">*</span></label>
                            <input type="text" wire:model="shiftName" placeholder="Ex: Administrativo 08h-18h" class="nx-form-input">
                            @error('shiftName')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group nx-form-group--full">
                            <label class="nx-form-label">Descrição</label>
                            <input type="text" wire:model="shiftDescription" placeholder="Descrição opcional..." class="nx-form-input">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">Hora Início <span class="nx-form-required">*</span></label>
                            <input type="time" wire:model="shiftStart" class="nx-form-input nx-form-input--time">
                            @error('shiftStart')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">Hora Fim <span class="nx-form-required">*</span></label>
                            <input type="time" wire:model="shiftEnd" class="nx-form-input nx-form-input--time">
                            @error('shiftEnd')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-form-label">Intervalo (minutos)</label>
                            <input type="number" wire:model="shiftBreak" min="0" max="480" class="nx-form-input">
                            @error('shiftBreak')<p class="nx-form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="$set('showShiftModal',false)" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="saveShift" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveShift">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ $editingShiftId ? 'Atualizar' : 'Criar Turno' }}
                        </span>
                        <span wire:loading wire:target="saveShift">Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

