<div class="nx-list-page" x-data="{ showModal: @entangle('showModal') }">

    {{-- ══════════════════════════════════════════
         HEADER
         ══════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Logs do Sistema</h1>
            <p class="nx-page-subtitle">Monitoramento de eventos e auditoria de ações realizadas no ERP</p>
        </div>
        <div class="nx-page-actions">
            <button wire:click="refresh" class="nx-btn nx-btn-outline" title="Atualizar">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                Atualizar
            </button>
            <button wire:click="exportCsv" class="nx-btn nx-btn-primary" title="Exportar CSV">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar CSV
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         KPI CARDS — 5 indicadores
         ══════════════════════════════════════════ --}}
    <div class="nx-log-kpis">

        {{-- Total de Logs --}}
        <div class="nx-kpi-card nx-log-accent-blue">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de Logs</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['total'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend" style="color:#64748B;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Todos os eventos registrados
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#EFF6FF;color:#3B82F6;border-color:transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
            </div>
        </div>

        {{-- Logs de Sucesso --}}
        <div class="nx-kpi-card nx-log-accent-green">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Logs de Sucesso</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['success'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend is-positive">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                        {{ $this->kpis['successPct'] }}% do total
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#DCFCE7;color:#15803D;border-color:transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            <div class="nx-log-kpi-bar"><div class="nx-log-kpi-bar-fill" style="width:{{ $this->kpis['successPct'] }}%;background:#22C55E;"></div></div>
        </div>

        {{-- Logs de Aviso --}}
        <div class="nx-kpi-card nx-log-accent-yellow">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Logs de Aviso</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['warning'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend" style="color:#A16207;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                        {{ $this->kpis['warningPct'] }}% do total
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#FEF9C3;color:#A16207;border-color:transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
            </div>
            <div class="nx-log-kpi-bar"><div class="nx-log-kpi-bar-fill" style="width:{{ $this->kpis['warningPct'] }}%;background:#EAB308;"></div></div>
        </div>

        {{-- Logs de Erro --}}
        <div class="nx-kpi-card nx-log-accent-red">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Logs de Erro</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['error'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend is-negative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        {{ $this->kpis['errorPct'] }}% do total
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#FEE2E2;color:#B91C1C;border-color:transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
            <div class="nx-log-kpi-bar"><div class="nx-log-kpi-bar-fill" style="width:{{ $this->kpis['errorPct'] }}%;background:#EF4444;"></div></div>
        </div>

        {{-- Usuários Ativos --}}
        <div class="nx-kpi-card nx-log-accent-cyan">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Usuários Ativos</p>
                    <h2 class="nx-kpi-card-value">{{ $this->kpis['activeUsers'] }}</h2>
                    <span class="nx-kpi-card-trend" style="color:#0369A1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Últimas 24 horas
                    </span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#E0F2FE;color:#0369A1;border-color:transparent;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         CARD PRINCIPAL — Filtros + Tabela
         ══════════════════════════════════════════ --}}
    <div class="nx-card">

        {{-- ── Filtros ─────────────────────────────────── --}}
        <div class="nx-log-filters">

            {{-- Busca rápida --}}
            <div class="nx-search-wrap" style="flex:1;min-width:200px;max-width:320px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="nx-search"
                    placeholder="Buscar em todos os campos…"
                >
            </div>

            {{-- Nível --}}
            <select wire:model.live="filterLevel" class="nx-filter-select">
                <option value="">Todos os níveis</option>
                <option value="success">Sucesso</option>
                <option value="warning">Aviso</option>
                <option value="error">Erro</option>
            </select>

            {{-- Módulo --}}
            <select wire:model.live="filterModule" class="nx-filter-select">
                <option value="">Todos os módulos</option>
                @foreach($this->modules as $mod)
                    <option value="{{ $mod }}">{{ $mod }}</option>
                @endforeach
            </select>

            {{-- Ação --}}
            <select wire:model.live="filterAction" class="nx-filter-select">
                <option value="">Todas as ações</option>
                @foreach($this->actions as $act)
                    <option value="{{ $act }}">{{ $act }}</option>
                @endforeach
            </select>

            {{-- Período --}}
            <div class="nx-log-date-range">
                <input type="date" wire:model.live="filterDateStart" class="nx-filter-select" title="Data inicial">
                <span class="nx-log-date-sep">até</span>
                <input type="date" wire:model.live="filterDateEnd"   class="nx-filter-select" title="Data final">
            </div>

            {{-- Limpar filtros --}}
            @if($search || $filterLevel || $filterModule || $filterAction || $filterDateStart || $filterDateEnd)
                <button wire:click="clearFilters" class="nx-btn nx-btn-ghost nx-btn-sm" title="Limpar filtros">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Limpar
                </button>
            @endif

        </div>

        {{-- ── Tabela ──────────────────────────────────── --}}
        @if($logs->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum registro encontrado',
                'description' => 'Nenhum log corresponde aos filtros aplicados. Tente ajustar a busca.',
            ])
        @else

        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th style="width:140px;">Data/Hora</th>
                        <th style="width:90px;">Nível</th>
                        <th>Usuário</th>
                        <th style="width:120px;">Ação</th>
                        <th style="width:110px;">Módulo</th>
                        <th>Descrição</th>
                        <th style="width:120px;">IP</th>
                        <th class="nx-th-actions" style="width:100px;">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr wire:key="log-{{ $log->id }}">

                        {{-- Data/Hora --}}
                        <td>
                            <span class="nx-log-datetime">{{ $log->created_at->format('d/m/Y') }}</span><br>
                            <span class="nx-log-time">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>

                        {{-- Nível --}}
                        <td>
                            <span class="nx-badge {{ $log->level_badge_class }}">
                                {{ $log->level_label }}
                            </span>
                        </td>

                        {{-- Usuário --}}
                        <td>
                            <div class="nx-log-user">
                                <div class="nx-log-user-avatar">
                                    {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="nx-log-user-info">
                                    <span class="nx-log-user-name">{{ $log->user_name ?? 'Sistema' }}</span>
                                    @if($log->user_email)
                                        <span class="nx-log-user-email">{{ $log->user_email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Ação --}}
                        <td>
                            <span class="nx-log-action">{{ $log->action }}</span>
                        </td>

                        {{-- Módulo --}}
                        <td>
                            <span class="nx-badge {{ $log->module_badge_class }}">{{ $log->module }}</span>
                        </td>

                        {{-- Descrição --}}
                        <td>
                            <span class="nx-log-desc" title="{{ $log->description }}">{{ $log->description }}</span>
                        </td>

                        {{-- IP --}}
                        <td>
                            <span style="font-family:monospace;font-size:12px;color:#64748B;">{{ $log->ip ?? '-' }}</span>
                        </td>

                        {{-- Detalhes --}}
                        <td class="nx-td-actions">
                            <button
                                type="button"
                                class="nx-action-btn nx-action-view"
                                title="Ver detalhes"
                                wire:click="openModal({{ $log->id }})"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Rodapé: contagem + paginação + per-page ─── --}}
        <div class="nx-table-footer">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span class="nx-table-count">
                    Mostrando {{ $logs->firstItem() }} a {{ $logs->lastItem() }} de {{ number_format($logs->total(), 0, ',', '.') }} registros
                </span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <label style="font-size:12.5px;color:#64748B;font-weight:500;">Por página:</label>
                    <select wire:model.live="perPage" class="nx-filter-select" style="padding:5px 8px;font-size:12.5px;">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="nx-pagination">
                {{ $logs->links() }}
            </div>
        </div>

        @endif
    </div>


    {{-- ══════════════════════════════════════════
         MODAL — Detalhes do Log
         ══════════════════════════════════════════ --}}
    <div
        class="nx-modal-overlay"
        x-show="showModal"
        x-transition:enter="nx-modal-enter"
        x-transition:enter-start="nx-modal-enter-start"
        x-transition:enter-end="nx-modal-enter-end"
        x-transition:leave="nx-modal-leave"
        x-transition:leave-start="nx-modal-leave-start"
        x-transition:leave-end="nx-modal-leave-end"
        @click.self="$wire.closeModal()"
        style="display:none;"
    >
        <div class="nx-modal nx-modal-lg">

            {{-- Cabeçalho --}}
            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:#EFF6FF;color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">Detalhes do Log</h2>
                        <p class="nx-modal-subtitle">Informações completas do evento registrado</p>
                    </div>
                </div>
                <button type="button" class="nx-modal-close" @click="$wire.closeModal()" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Corpo --}}
            @if($this->selectedLog)
            @php $log = $this->selectedLog; @endphp
            <div class="nx-modal-body">

                {{-- Badges de nível e módulo --}}
                <div style="display:flex;gap:8px;margin-bottom:20px;">
                    <span class="nx-badge {{ $log->level_badge_class }}" style="font-size:12.5px;padding:4px 12px;">{{ $log->level_label }}</span>
                    <span class="nx-badge {{ $log->module_badge_class }}" style="font-size:12.5px;padding:4px 12px;">{{ $log->module }}</span>
                    <span class="nx-badge nx-badge-neutral" style="font-size:12.5px;padding:4px 12px;font-family:monospace;">{{ $log->action }}</span>
                </div>

                <div class="nx-log-detail-grid">

                    <div class="nx-log-detail-item">
                        <span class="nx-log-detail-label">Data/Hora</span>
                        <span class="nx-log-detail-value">{{ $log->created_at->format('d/m/Y \à\s H:i:s') }}</span>
                    </div>

                    <div class="nx-log-detail-item">
                        <span class="nx-log-detail-label">Endereço IP</span>
                        <span class="nx-log-detail-value" style="font-family:monospace;">{{ $log->ip ?? '-' }}</span>
                    </div>

                    <div class="nx-log-detail-item">
                        <span class="nx-log-detail-label">Usuário</span>
                        <span class="nx-log-detail-value">{{ $log->user_name ?? 'Sistema' }}</span>
                    </div>

                    <div class="nx-log-detail-item">
                        <span class="nx-log-detail-label">E-mail</span>
                        <span class="nx-log-detail-value">{{ $log->user_email ?? '-' }}</span>
                    </div>

                    <div class="nx-log-detail-item" style="grid-column:1/-1;">
                        <span class="nx-log-detail-label">Descrição</span>
                        <span class="nx-log-detail-value">{{ $log->description }}</span>
                    </div>

                </div>

                {{-- Contexto JSON --}}
                @if($log->context)
                <div class="nx-log-json-wrap">
                    <div class="nx-log-json-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Contexto / Payload JSON
                    </div>
                    <pre class="nx-log-json-body">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                @else
                <div class="nx-log-json-wrap">
                    <div class="nx-log-json-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Contexto / Payload JSON
                    </div>
                    <pre class="nx-log-json-body nx-log-json-empty">Nenhum contexto adicional registrado.</pre>
                </div>
                @endif

            </div>
            @endif

            {{-- Rodapé --}}
            <div class="nx-modal-footer">
                <button type="button" class="nx-btn nx-btn-outline" @click="$wire.closeModal()">Fechar</button>
            </div>

        </div>
    </div>

</div>

