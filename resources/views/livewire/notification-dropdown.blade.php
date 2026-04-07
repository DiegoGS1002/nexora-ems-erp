{{-- ═══════════════════════════════════════════════════════
     NOTIFICATION DROPDOWN — Nexora ERP
     Glassmorphism popover acionado pelo sino na navbar
     ═══════════════════════════════════════════════════════ --}}
<div class="nx-notif-trigger-wrap"
     x-data="{ open: @entangle('isOpen') }"
     @click.outside="open = false; $wire.close()">

    {{-- ── Botão Sino ── --}}
    <button
        wire:click="toggle"
        class="nx-sb-icon-btn nx-notif-btn"
        title="Notificações"
        aria-label="Notificações"
    >
        <span class="nx-sb-icon" style="position:relative;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            @if($this->unreadCount > 0)
                <span class="nx-notif-dot"></span>
            @endif
        </span>
        <span class="nx-sb-label">Notificações</span>
        @if($this->unreadCount > 0)
            <span class="nx-sb-badge">{{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}</span>
        @endif
    </button>

    {{-- ── Painel Flutuante (Glassmorphism) ── --}}
    <div
        class="nx-notif-panel"
        x-show="open"
        x-transition:enter="nx-notif-panel-enter"
        x-transition:enter-start="nx-notif-panel-enter-start"
        x-transition:enter-end="nx-notif-panel-enter-end"
        x-transition:leave="nx-notif-panel-leave"
        x-transition:leave-start="nx-notif-panel-leave-start"
        x-transition:leave-end="nx-notif-panel-leave-end"
        style="display:none;"
    >
        {{-- Header do painel --}}
        <div class="nx-notif-panel-header">
            <div class="nx-notif-panel-title-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <h3 class="nx-notif-panel-title">Notificações</h3>
                @if($this->unreadCount > 0)
                    <span class="nx-notif-count-badge">{{ $this->unreadCount }}</span>
                @endif
            </div>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="nx-notif-clear-btn" title="Marcar todas como lidas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Marcar tudo
                </button>
            @endif
        </div>

        {{-- Lista de Notificações --}}
        <div class="nx-notif-list">
            @forelse($this->notifications as $notification)
                @php
                    $data    = $notification->data ?? [];
                    $type    = $data['type']    ?? 'info';
                    $message = $data['message'] ?? ($data['text'] ?? 'Sem descrição');
                    $title   = $data['title']   ?? null;
                    $icon    = match($type) {
                        'stock'   => 'package',
                        'sale'    => 'dollar',
                        'warning' => 'alert',
                        'success' => 'check',
                        'error'   => 'x-circle',
                        'fiscal'  => 'file-text',
                        'license' => 'shield',
                        default   => 'bell',
                    };
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div class="nx-notif-item {{ $isUnread ? 'nx-notif-item--unread' : '' }}"
                     wire:key="notif-{{ $notification->id }}">

                    {{-- Ícone por tipo --}}
                    <div class="nx-notif-icon nx-notif-icon--{{ $type }}">
                        @if($icon === 'package')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        @elseif($icon === 'dollar')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        @elseif($icon === 'alert')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        @elseif($icon === 'check')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif($icon === 'x-circle')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        @elseif($icon === 'file-text')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        @elseif($icon === 'shield')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        @endif
                    </div>

                    {{-- Conteúdo --}}
                    <div class="nx-notif-content">
                        @if($title)
                            <p class="nx-notif-title">{{ $title }}</p>
                        @endif
                        <p class="nx-notif-message">{{ $message }}</p>
                        <span class="nx-notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Ação: marcar como lida --}}
                    @if($isUnread)
                        <button
                            wire:click="markAsRead('{{ $notification->id }}')"
                            class="nx-notif-read-btn"
                            title="Marcar como lida"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </button>
                    @endif

                </div>
            @empty
                <div class="nx-notif-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.25">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>Nenhuma notificação nova.</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="nx-notif-panel-footer">
            <a href="{{ route('notifications.index') }}" class="nx-notif-all-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M3 9h18M9 21V9"/>
                </svg>
                Ver todas as notificações
            </a>
        </div>
    </div>
</div>

