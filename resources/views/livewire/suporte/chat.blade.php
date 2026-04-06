<div class="nx-chat-page" wire:poll.10s="atualizarChat">

    {{-- CABEÇALHO DA PÁGINA --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Chat de Suporte</h1>
            <p class="nx-page-subtitle">Abra e acompanhe seus tickets de suporte técnico.</p>
        </div>
        <div class="nx-page-actions">
            @unless(auth()->user()->is_admin)
            <button type="button" wire:click="abrirNovoTicket" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Novo Ticket
            </button>
            @endunless
        </div>
    </div>

    {{-- NOTIFICAÇÃO FLASH --}}
    @session('success')
        <div class="nx-alert nx-alert--success" x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)" style="margin-bottom:16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
            {{ $value }}
        </div>
    @endsession

    {{-- LAYOUT PRINCIPAL DO CHAT --}}
    <div class="nx-chat-layout">

        {{-- ─── PAINEL ESQUERDO — Lista de Tickets ─── --}}
        <aside class="nx-chat-sidebar">

            {{-- Busca e Filtro --}}
            <div class="nx-chat-sidebar-header">
                <div class="nx-chat-search-wrap">
                    <svg class="nx-chat-search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="busca"
                        placeholder="Buscar tickets..."
                        class="nx-chat-search-input"
                    >
                </div>

                <select wire:model.live="filtroStatus" class="nx-chat-filter-select">
                    <option value="">Todos os status</option>
                    @foreach($this->statusOpcoes as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Lista de Tickets --}}
            <div class="nx-chat-ticket-list">
                @forelse($this->tickets as $ticket)
                    @php
                        $ativo = $ticketSelecionadoId === $ticket->id;
                        $ultima = $ticket->mensagens->first();
                    @endphp
                    <button
                        type="button"
                        wire:key="ticket-{{ $ticket->id }}"
                        wire:click="selecionarTicket('{{ $ticket->id }}')"
                        class="nx-chat-ticket-item {{ $ativo ? 'nx-chat-ticket-item--active' : '' }}"
                    >
                        <div class="nx-chat-ticket-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>

                        <div class="nx-chat-ticket-info">
                            <div class="nx-chat-ticket-top">
                                <span class="nx-chat-ticket-assunto">{{ $ticket->assunto }}</span>
                                <span class="nx-chat-badge {{ $ticket->status->cssClass() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </div>
                            <div class="nx-chat-ticket-meta">
                                <span class="nx-chat-prio {{ $ticket->prioridade->cssClass() }}">
                                    {{ $ticket->prioridade->label() }}
                                </span>
                                @if($ultima)
                                    <span class="nx-chat-ticket-preview">
                                        {{ str($ultima->conteudo)->limit(45) }}
                                    </span>
                                @endif
                            </div>
                            <div class="nx-chat-ticket-date">
                                {{ $ticket->created_at->diffForHumans() }}
                                @if(auth()->user()->is_admin)
                                    &middot; {{ $ticket->user->name }}
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="nx-chat-empty-list">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                             stroke-linejoin="round" style="color:#CBD5E1;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <p>Nenhum ticket encontrado</p>
                        @unless(auth()->user()->is_admin)
                            <button type="button" wire:click="abrirNovoTicket" class="nx-btn nx-btn-sm nx-btn-primary" style="margin-top:8px;">
                                Abrir primeiro ticket
                            </button>
                        @endunless
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- ─── PAINEL DIREITO — Conversa ─── --}}
        <section class="nx-chat-main">
            @if($this->ticketAtivo)
                @php $ticket = $this->ticketAtivo; @endphp

                {{-- Cabeçalho do chat --}}
                <div class="nx-chat-conv-header">
                    <div class="nx-chat-conv-header-left">
                        <div class="nx-chat-conv-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="nx-chat-conv-title">{{ $ticket->assunto }}</h3>
                            <p class="nx-chat-conv-sub">
                                Aberto por <strong>{{ $ticket->user->name }}</strong>
                                &middot; {{ $ticket->created_at->format('d/m/Y \à\s H:i') }}
                                @if($ticket->categoria)
                                    &middot; {{ $ticket->categoria }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="nx-chat-conv-actions">
                        <span class="nx-chat-prio {{ $ticket->prioridade->cssClass() }} nx-chat-prio--lg">
                            {{ $ticket->prioridade->label() }}
                        </span>

                        @if(auth()->user()->is_admin)
                            <select
                                wire:change="atualizarStatus($event.target.value)"
                                class="nx-chat-status-select nx-chat-badge {{ $ticket->status->cssClass() }}"
                            >
                                @foreach($this->statusOpcoes as $s)
                                    <option
                                        value="{{ $s->value }}"
                                        @selected($ticket->status === $s)
                                    >{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        @else
                            <span class="nx-chat-badge {{ $ticket->status->cssClass() }}">
                                {{ $ticket->status->label() }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Mensagens --}}
                <div class="nx-chat-messages" id="nx-chat-messages">
                    @forelse($this->mensagensAtivas as $msg)
                        @php
                            $minha = $msg->user_id === auth()->id();
                        @endphp
                        <div
                            wire:key="msg-{{ $msg->id }}"
                            class="nx-chat-msg-wrap {{ $minha ? 'nx-chat-msg-wrap--mine' : '' }}"
                        >
                            @unless($minha)
                                <div class="nx-chat-msg-avatar {{ $msg->is_suporte ? 'nx-chat-msg-avatar--support' : '' }}"
                                     title="{{ $msg->user->name }}">
                                    {{ str($msg->user->name)->upper()->substr(0, 1) }}
                                </div>
                            @endunless

                            <div class="nx-chat-msg-col">
                                @unless($minha)
                                    <span class="nx-chat-msg-author">
                                        {{ $msg->user->name }}
                                        @if($msg->is_suporte)
                                            <span class="nx-chat-support-tag">Suporte</span>
                                        @endif
                                    </span>
                                @endunless

                                <div class="nx-chat-bubble {{ $minha ? 'nx-chat-bubble--mine' : ($msg->is_suporte ? 'nx-chat-bubble--support' : '') }}">
                                    {!! nl2br(e($msg->conteudo)) !!}
                                </div>

                                <span class="nx-chat-msg-time {{ $minha ? 'nx-chat-msg-time--right' : '' }}">
                                    {{ $msg->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="nx-chat-no-messages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="1.2" style="color:#CBD5E1;">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <p>Nenhuma mensagem ainda. Seja o primeiro a responder.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Área de envio --}}
                @if($ticket->isAberto())
                    <div class="nx-chat-input-area">
                        <textarea
                            wire:model="novaMensagemTexto"
                            wire:keydown.ctrl.enter="enviarMensagem"
                            class="nx-chat-textarea"
                            placeholder="Digite sua mensagem... (Ctrl+Enter para enviar)"
                            rows="3"
                        ></textarea>
                        <div class="nx-chat-input-footer">
                            <span class="nx-chat-input-hint">
                                <kbd>Ctrl</kbd> + <kbd>Enter</kbd> para enviar
                            </span>
                            <button
                                type="button"
                                wire:click="enviarMensagem"
                                wire:loading.attr="disabled"
                                class="nx-btn nx-btn-primary nx-btn-sm"
                            >
                                <span wire:loading.remove wire:target="enviarMensagem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Enviar
                                </span>
                                <span wire:loading wire:target="enviarMensagem">Enviando...</span>
                            </button>
                        </div>
                        @error('novaMensagemTexto')
                            <p class="nx-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="nx-chat-closed-notice">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                        Ticket encerrado em {{ $ticket->fechado_em?->format('d/m/Y \à\s H:i') ?? $ticket->updated_at->format('d/m/Y \à\s H:i') }}.
                        @if(auth()->user()->is_admin)
                            <button wire:click="atualizarStatus('{{ \App\Enums\StatusTicketSuporte::Aberto->value }}')" class="nx-link">
                                Reabrir ticket
                            </button>
                        @endif
                    </div>
                @endif

            @else
                {{-- Estado vazio — nenhum ticket selecionado --}}
                <div class="nx-chat-empty-conv">
                    <div class="nx-chat-empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3>Selecione um ticket</h3>
                    <p>Escolha um ticket na lista ao lado para visualizar a conversa.</p>
                    @unless(auth()->user()->is_admin)
                        <button type="button" wire:click="abrirNovoTicket" class="nx-btn nx-btn-primary" style="margin-top:16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Abrir novo ticket
                        </button>
                    @endunless
                </div>
            @endif
        </section>
    </div>

    {{-- MODAL — NOVO TICKET --}}
    @if($showNovoTicket)
        <div class="nx-modal-overlay" wire:click.self="fecharNovoTicket">
            <div class="nx-modal nx-modal--md">

                <div class="nx-modal-header">
                    <h4 class="nx-modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" style="display:inline;vertical-align:-2px;margin-right:6px;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Abrir novo ticket de suporte
                    </h4>
                    <button type="button" wire:click="fecharNovoTicket" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <div class="nx-modal-body">
                    <div class="nx-form-grid">
                        {{-- Assunto --}}
                        <div class="nx-field nx-field--full">
                            <label>Assunto <span class="nx-required">*</span></label>
                            <input
                                type="text"
                                wire:model="novoTicket.assunto"
                                placeholder="Descreva brevemente o problema..."
                                class="@error('novoTicket.assunto') is-invalid @enderror"
                            >
                            @error('novoTicket.assunto') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Prioridade --}}
                        <div class="nx-field">
                            <label>Prioridade <span class="nx-required">*</span></label>
                            <select wire:model="novoTicket.prioridade" class="@error('novoTicket.prioridade') is-invalid @enderror">
                                @foreach($this->prioridadeOpcoes as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                            @error('novoTicket.prioridade') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Categoria --}}
                        <div class="nx-field">
                            <label>Categoria <span class="nx-text-muted">(opcional)</span></label>
                            <input type="text" wire:model="novoTicket.categoria" placeholder="Ex: Financeiro, Acesso, Bug...">
                        </div>

                        {{-- Mensagem inicial --}}
                        <div class="nx-field nx-field--full">
                            <label>Descreva seu problema <span class="nx-required">*</span></label>
                            <textarea
                                wire:model="novoTicket.mensagem"
                                rows="5"
                                placeholder="Explique detalhadamente o que está acontecendo..."
                                class="@error('novoTicket.mensagem') is-invalid @enderror"
                            ></textarea>
                            @error('novoTicket.mensagem') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="nx-modal-footer">
                    <button type="button" wire:click="fecharNovoTicket" class="nx-btn nx-btn-ghost">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="criarTicket"
                        wire:loading.attr="disabled"
                        class="nx-btn nx-btn-primary"
                    >
                        <span wire:loading.remove wire:target="criarTicket">Abrir Ticket</span>
                        <span wire:loading wire:target="criarTicket">Criando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- Script para scroll automático --}}
@push('scripts')
<script>
    function scrollChatToBottom() {
        const el = document.getElementById('nx-chat-messages');
        if (el) el.scrollTop = el.scrollHeight;
    }

    document.addEventListener('livewire:navigated', scrollChatToBottom);
    document.addEventListener('DOMContentLoaded', scrollChatToBottom);

    Livewire.on('mensagem-enviada', () => {
        setTimeout(scrollChatToBottom, 80);
    });

    // Scroll ao atualizar mensagens via poll
    const observer = new MutationObserver(() => {
        const el = document.getElementById('nx-chat-messages');
        if (el) {
            const isAtBottom = el.scrollHeight - el.clientHeight <= el.scrollTop + 60;
            if (isAtBottom) scrollChatToBottom();
        }
    });

    const chatEl = document.getElementById('nx-chat-messages');
    if (chatEl) observer.observe(chatEl, { childList: true, subtree: true });
</script>
@endpush

