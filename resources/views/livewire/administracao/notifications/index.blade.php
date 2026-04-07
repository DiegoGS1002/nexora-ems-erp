<div class="nx-list-page" x-data="{}">

    {{-- ══════════════════════════════════════════
         HEADER DA PÁGINA
         ══════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Central de Notificações</h1>
            <p class="nx-page-subtitle">Gerencie todos os alertas e avisos do sistema em um só lugar</p>
        </div>
        <div class="nx-page-actions">
            @if($this->kpis['unread'] > 0)
                <button wire:click="markAllAsRead" class="nx-btn nx-btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Marcar todas como lidas
                </button>
            @endif
            @if($this->kpis['total'] > 0)
                <button wire:click="deleteAll"
                        wire:confirm="Deseja remover TODAS as notificações? Esta ação não pode ser desfeita."
                        class="nx-btn nx-btn-danger-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Limpar tudo
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         KPI CARDS
         ══════════════════════════════════════════ --}}
    <div class="nx-notif-kpis">

        {{-- Total --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['total'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend" style="color:#64748B;">Todas as notificações</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#EFF6FF;color:#3B82F6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
            </div>
        </div>

        {{-- Não lidas --}}
        <div class="nx-kpi-card nx-log-accent-yellow">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Não lidas</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['unread'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend" style="color:#A16207;">Aguardando revisão</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#FEF9C3;color:#A16207;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>

        {{-- Lidas --}}
        <div class="nx-kpi-card nx-log-accent-green">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Lidas</p>
                    <h2 class="nx-kpi-card-value">{{ number_format($this->kpis['read'], 0, ',', '.') }}</h2>
                    <span class="nx-kpi-card-trend is-positive">Já visualizadas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:#DCFCE7;color:#15803D;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         CARD PRINCIPAL — Filtros + Lista
         ══════════════════════════════════════════ --}}
    <div class="nx-card">

        {{-- ── Filtros ── --}}
        <div class="nx-log-filters">

            {{-- Status de leitura --}}
            <select wire:model.live="filterRead" class="nx-filter-select">
                <option value="">Todas</option>
                <option value="unread">Não lidas</option>
                <option value="read">Lidas</option>
            </select>

            {{-- Tipo --}}
            @if(count($this->types) > 0)
                <select wire:model.live="filterType" class="nx-filter-select">
                    <option value="">Todos os tipos</option>
                    @foreach($this->types as $t)
                        <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Data --}}
            <input type="date" wire:model.live="filterDate" class="nx-filter-select" title="Filtrar por data">

            {{-- Limpar filtros --}}
            @if($filterRead || $filterType || $filterDate)
                <button wire:click="$set('filterRead',''); $set('filterType',''); $set('filterDate','')"
                        class="nx-btn nx-btn-ghost nx-btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Limpar
                </button>
            @endif

        </div>

        {{-- ── Lista de Notificações ── --}}
        @if($notifications->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhuma notificação encontrada',
                'description' => 'Não há notificações correspondentes aos filtros aplicados.',
            ])
        @else
            <div class="nx-notif-page-list">
                @foreach($notifications as $notification)
                    @php
                        $data    = $notification->data ?? [];
                        $type    = $data['type']    ?? 'info';
                        $message = $data['message'] ?? ($data['text'] ?? 'Sem descrição');
                        $title   = $data['title']   ?? null;
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <div class="nx-notif-row {{ $isUnread ? 'nx-notif-row--unread' : '' }}"
                         wire:key="notif-page-{{ $notification->id }}">

                        {{-- Indicador de não lida --}}
                        @if($isUnread)
                            <span class="nx-notif-row-dot"></span>
                        @else
                            <span class="nx-notif-row-dot nx-notif-row-dot--read"></span>
                        @endif

                        {{-- Ícone de tipo --}}
                        <div class="nx-notif-row-icon nx-notif-icon--{{ $type }}">
                            @switch($type)
                                @case('stock')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                    @break
                                @case('sale')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    @break
                                @case('warning')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    @break
                                @case('error')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    @break
                                @case('success')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    @break
                                @case('fiscal')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    @break
                                @case('license')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @endswitch
                        </div>

                        {{-- Conteúdo --}}
                        <div class="nx-notif-row-body">
                            <div class="nx-notif-row-top">
                                @if($title)
                                    <span class="nx-notif-row-title">{{ $title }}</span>
                                @endif
                                <span class="nx-notif-row-type-badge nx-notif-badge--{{ $type }}">
                                    {{ ucfirst($type) }}
                                </span>
                                @if($isUnread)
                                    <span class="nx-notif-unread-badge">Nova</span>
                                @endif
                            </div>
                            <p class="nx-notif-row-message">{{ $message }}</p>
                            <span class="nx-notif-row-time">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $notification->created_at->format('d/m/Y \à\s H:i') }}
                                · {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Ações --}}
                        <div class="nx-notif-row-actions">
                            @if($isUnread)
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="nx-action-btn nx-action-view"
                                    title="Marcar como lida"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                            @endif
                            <button
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                wire:confirm="Remover esta notificação?"
                                class="nx-action-btn nx-action-delete"
                                title="Remover"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Rodapé paginação ── --}}
            <div class="nx-table-footer">
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <span class="nx-table-count">
                        Mostrando {{ $notifications->firstItem() }} a {{ $notifications->lastItem() }}
                        de {{ number_format($notifications->total(), 0, ',', '.') }} notificações
                    </span>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:12.5px;color:#64748B;font-weight:500;">Por página:</label>
                        <select wire:model.live="perPage" class="nx-filter-select" style="padding:5px 8px;font-size:12.5px;">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
                <div class="nx-pagination">
                    {{ $notifications->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

