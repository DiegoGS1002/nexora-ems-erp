<div class="nx-chat-page" wire:poll.8s="atualizarChat">

    {{-- CABEÇALHO DA PÁGINA --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#6366F1,#8B5CF6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                        <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                    </svg>
                </div>
                <div>
                    <h1 class="nx-page-title">Suporte com IA</h1>
                    <p class="nx-page-subtitle">
                        @if(auth()->user()->is_admin)
                            Histórico completo de atendimentos — respostas automáticas via IA
                        @else
                            Seu assistente inteligente Nexora responde em instantes
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="nx-page-actions" style="display:flex;gap:8px;align-items:center;">
            @unless(auth()->user()->is_admin)
                <a href="https://wa.me/5532984502345?text=Olá,%20preciso%20de%20suporte%20humano%20no%20Nexora%20ERP"
                   target="_blank" rel="noopener noreferrer"
                   class="nx-btn"
                   style="display:inline-flex;align-items:center;gap:6px;background:#25D366;color:#fff;border:none;text-decoration:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;"
                   title="Falar com atendente humano via WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                    </svg>
                    Falar com Atendente
                </a>
                <button type="button" wire:click="abrirNovoTicket" class="nx-btn nx-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Novo Ticket
                </button>
            @endunless
        </div>
    </div>

    {{-- NOTIFICAÇÃO TOAST (não desloca o layout) --}}
    @if($flashSucesso)
        <div x-data="{ show: false }"
             x-init="
                 setTimeout(() => show = true, 50);
                 setTimeout(() => show = false, 4000);
             "
             @flash-sucesso.window="show = true; setTimeout(() => show = false, 4000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;align-items:center;gap:10px;background:#10B981;color:#fff;padding:12px 18px;border-radius:12px;box-shadow:0 8px 32px rgba(16,185,129,0.35);font-size:14px;font-weight:500;max-width:340px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;">
                <path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
            {{ $flashSucesso }}
        </div>
    @endif

    {{-- LAYOUT PRINCIPAL DO CHAT --}}
    <div class="nx-chat-layout">

        {{-- ─── PAINEL ESQUERDO — Lista de Tickets ─── --}}
        <aside class="nx-chat-sidebar">
            <div class="nx-chat-sidebar-header">
                <div class="nx-chat-search-wrap">
                    <svg class="nx-chat-search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar tickets..." class="nx-chat-search-input">
                </div>
                <select wire:model.live="filtroStatus" class="nx-chat-filter-select">
                    <option value="">Todos os status</option>
                    @foreach($this->statusOpcoes as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="nx-chat-ticket-list">
                @forelse($this->tickets as $ticket)
                    @php $ativo = $ticketSelecionadoId === $ticket->id; $ultima = $ticket->mensagens->first(); @endphp
                    <div class="nx-chat-ticket-item-wrap">
                        <button type="button" wire:key="ticket-{{ $ticket->id }}" wire:click="selecionarTicket('{{ $ticket->id }}')"
                            class="nx-chat-ticket-item {{ $ativo ? 'nx-chat-ticket-item--active' : '' }}">
                            <div class="nx-chat-ticket-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <div class="nx-chat-ticket-info">
                                <div class="nx-chat-ticket-top">
                                    <span class="nx-chat-ticket-assunto">{{ $ticket->assunto }}</span>
                                    <span class="nx-chat-badge {{ $ticket->status->cssClass() }}">{{ $ticket->status->label() }}</span>
                                </div>
                                <div class="nx-chat-ticket-meta">
                                    <span class="nx-chat-prio {{ $ticket->prioridade->cssClass() }}">{{ $ticket->prioridade->label() }}</span>
                                    @if($ultima)
                                        <span class="nx-chat-ticket-preview">{{ str($ultima->conteudo)->limit(45) }}</span>
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

                        @unless(auth()->user()->is_admin)
                            <button type="button"
                                class="nx-chat-ticket-delete"
                                wire:click="excluirTicket('{{ $ticket->id }}')"
                                onclick="if(!confirm('Excluir este chat? Isso remove todo o histórico.')) { event.stopImmediatePropagation(); return false; }"
                                title="Excluir chat">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        @endunless
                    </div>
                @empty
                    <div class="nx-chat-empty-list">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:#CBD5E1;">
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
                        <div class="nx-chat-conv-icon" style="background:linear-gradient(135deg,#6366F1,#8B5CF6);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="nx-chat-conv-title">{{ $ticket->assunto }}</h3>
                            <p class="nx-chat-conv-sub">
                                Aberto por <strong>{{ $ticket->user->name }}</strong>
                                &middot; {{ $ticket->created_at->format('d/m/Y \à\s H:i') }}
                                @if($ticket->categoria) &middot; {{ $ticket->categoria }} @endif
                            </p>
                        </div>
                    </div>
                    <div class="nx-chat-conv-actions">
                        <span class="nx-chat-prio {{ $ticket->prioridade->cssClass() }} nx-chat-prio--lg">{{ $ticket->prioridade->label() }}</span>
                        @if(auth()->user()->is_admin)
                            <select wire:change="atualizarStatus($event.target.value)" class="nx-chat-status-select nx-chat-badge {{ $ticket->status->cssClass() }}">
                                @foreach($this->statusOpcoes as $s)
                                    <option value="{{ $s->value }}" @selected($ticket->status === $s)>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        @else
                            <span class="nx-chat-badge {{ $ticket->status->cssClass() }}">{{ $ticket->status->label() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Banner IA --}}
                @unless(auth()->user()->is_admin)
                <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;background:linear-gradient(90deg,rgba(99,102,241,0.08),rgba(139,92,246,0.06));border-bottom:1px solid rgba(99,102,241,0.12);font-size:12px;color:#6366F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                        <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                    </svg>
                    <span><strong>Nexora IA</strong> está respondendo automaticamente. Precisa de suporte humano?
                        <a href="https://wa.me/5532984502345?text=Olá,%20preciso%20de%20suporte%20humano%20no%20Nexora%20ERP"
                           target="_blank" rel="noopener noreferrer"
                           style="color:#25D366;font-weight:600;text-decoration:none;">
                           Clique aqui para WhatsApp
                        </a>
                    </span>
                </div>
                @endunless

                {{-- Mensagens --}}
                <div class="nx-chat-messages" id="nx-chat-messages">
                    @forelse($this->mensagensAtivas as $msg)
                        @php
                            $ehIA   = $msg->is_ia;
                            $minha  = ($msg->user_id === auth()->id()) && ! $ehIA;
                        @endphp
                        <div wire:key="msg-{{ $msg->id }}" class="nx-chat-msg-wrap {{ $minha ? 'nx-chat-msg-wrap--mine' : '' }}">
                            @unless($minha)
                                @if($ehIA)
                                    <div class="nx-chat-msg-avatar nx-chat-msg-avatar--ia" title="Nexora IA">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                            <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                                            <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="nx-chat-msg-avatar {{ $msg->is_suporte ? 'nx-chat-msg-avatar--support' : '' }}" title="{{ $msg->user->name }}">
                                        {{ str($msg->user->name)->upper()->substr(0, 1) }}
                                    </div>
                                @endif
                            @endunless

                            <div class="nx-chat-msg-col">
                                @unless($minha)
                                    <span class="nx-chat-msg-author">
                                        @if($ehIA)
                                            Nexora IA <span class="nx-chat-ia-tag">IA</span>
                                        @else
                                            {{ $msg->user->name }}
                                            @if($msg->is_suporte) <span class="nx-chat-support-tag">Suporte</span> @endif
                                        @endif
                                    </span>
                                @endunless
                                <div class="nx-chat-bubble {{ $minha ? 'nx-chat-bubble--mine' : ($ehIA ? 'nx-chat-bubble--ia' : ($msg->is_suporte ? 'nx-chat-bubble--support' : '')) }}">
                                    {!! nl2br(e($msg->conteudo)) !!}
                                </div>
                                <span class="nx-chat-msg-time {{ $minha ? 'nx-chat-msg-time--right' : '' }}">
                                    {{ $msg->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="nx-chat-no-messages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:#CBD5E1;">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <p>Nenhuma mensagem ainda.</p>
                        </div>
                    @endforelse

                    {{-- Indicador IA digitando --}}
                    @if($iaRespondendo)
                        <div class="nx-chat-msg-wrap">
                            <div class="nx-chat-msg-avatar nx-chat-msg-avatar--ia">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                    <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                                    <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                                </svg>
                            </div>
                            <div class="nx-chat-msg-col">
                                <span class="nx-chat-msg-author">Nexora IA <span class="nx-chat-ia-tag">IA</span></span>
                                <div class="nx-chat-bubble nx-chat-bubble--ia" style="padding:10px 14px;">
                                    <span class="nx-ia-typing-dots"><span></span><span></span><span></span></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Área de envio --}}
                @if($ticket->isAberto())
                    <div class="nx-chat-input-area">
                        <textarea wire:model="novaMensagemTexto" wire:keydown.ctrl.enter="enviarMensagem"
                            class="nx-chat-textarea" placeholder="Digite sua mensagem... (Ctrl+Enter para enviar)" rows="3"></textarea>
                        <div class="nx-chat-input-footer">
                            <span class="nx-chat-input-hint">
                                @unless(auth()->user()->is_admin)
                                    <a href="https://wa.me/5532984502345?text=Olá,%20preciso%20de%20suporte%20humano%20no%20Nexora%20ERP"
                                       target="_blank" rel="noopener noreferrer"
                                       style="display:inline-flex;align-items:center;gap:4px;color:#25D366;font-size:12px;text-decoration:none;font-weight:500;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                                        </svg>
                                        Falar com humano
                                    </a>
                                @else
                                    <kbd>Ctrl</kbd> + <kbd>Enter</kbd> para enviar
                                @endunless
                            </span>
                            <button type="button" wire:click="enviarMensagem" wire:loading.attr="disabled" class="nx-btn nx-btn-primary nx-btn-sm">
                                <span wire:loading.remove wire:target="enviarMensagem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Enviar
                                </span>
                                <span wire:loading wire:target="enviarMensagem">Analisando...</span>
                            </button>
                        </div>
                        @error('novaMensagemTexto') <p class="nx-field-error">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="nx-chat-closed-notice">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                        Ticket encerrado em {{ $ticket->fechado_em?->format('d/m/Y \à\s H:i') ?? $ticket->updated_at->format('d/m/Y \à\s H:i') }}.
                        @if(auth()->user()->is_admin)
                            <button wire:click="atualizarStatus('{{ \App\Enums\StatusTicketSuporte::EmAndamento->value }}')" class="nx-link">
                                Reabrir ticket
                            </button>
                        @endif
                    </div>
                @endif

            @else
                <div class="nx-chat-empty-conv">
                    <div class="nx-chat-empty-icon" style="background:linear-gradient(135deg,rgba(99,102,241,0.1),rgba(139,92,246,0.1));border-radius:50%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="1.2">
                            <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                            <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                        </svg>
                    </div>
                    @if(auth()->user()->is_admin)
                        <h3>Central de Atendimentos</h3>
                        <p>Selecione um ticket para visualizar a conversa e o atendimento da IA.</p>
                    @else
                        <h3>Suporte Inteligente Nexora</h3>
                        <p>Nossa IA responde instantaneamente. Selecione um ticket ou abra um novo.</p>
                        <div style="display:flex;gap:10px;margin-top:16px;justify-content:center;flex-wrap:wrap;">
                            <button type="button" wire:click="abrirNovoTicket" class="nx-btn nx-btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Abrir novo ticket
                            </button>
                            <a href="https://wa.me/5532984502345?text=Olá,%20preciso%20de%20suporte%20humano%20no%20Nexora%20ERP"
                               target="_blank" rel="noopener noreferrer"
                               style="display:inline-flex;align-items:center;gap:6px;background:#25D366;color:#fff;border:none;text-decoration:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                                </svg>
                                Falar com Atendente
                            </a>
                        </div>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-2px;margin-right:6px;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Abrir novo ticket de suporte
                    </h4>
                    <button type="button" wire:click="fecharNovoTicket" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:linear-gradient(90deg,rgba(99,102,241,0.08),rgba(139,92,246,0.06));border-radius:8px;margin-bottom:16px;font-size:12px;color:#6366F1;border:1px solid rgba(99,102,241,0.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                            <circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/>
                        </svg>
                        <span>A <strong>Nexora IA</strong> responderá automaticamente após a abertura do ticket.</span>
                    </div>
                    <div class="nx-form-grid">
                        <div class="nx-field nx-field--full">
                            <label>Assunto <span class="nx-required">*</span></label>
                            <input type="text" wire:model="novoTicket.assunto" placeholder="Descreva brevemente o problema..." class="@error('novoTicket.assunto') is-invalid @enderror">
                            @error('novoTicket.assunto') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Prioridade <span class="nx-required">*</span></label>
                            <select wire:model="novoTicket.prioridade" class="@error('novoTicket.prioridade') is-invalid @enderror">
                                @foreach($this->prioridadeOpcoes as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                            @error('novoTicket.prioridade') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Categoria <span class="nx-text-muted">(opcional)</span></label>
                            <input type="text" wire:model="novoTicket.categoria" placeholder="Ex: Financeiro, Acesso, Bug...">
                        </div>
                        <div class="nx-field nx-field--full">
                            <label>Descreva seu problema <span class="nx-required">*</span></label>
                            <textarea wire:model="novoTicket.mensagem" rows="5" placeholder="Explique detalhadamente o que está acontecendo..." class="@error('novoTicket.mensagem') is-invalid @enderror"></textarea>
                            @error('novoTicket.mensagem') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="fecharNovoTicket" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="criarTicket" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                        <span wire:loading.remove wire:target="criarTicket">Abrir Ticket</span>
                        <span wire:loading wire:target="criarTicket">Criando e consultando IA...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function scrollChatToBottom() {
        const el = document.getElementById('nx-chat-messages');
        if (el) el.scrollTop = el.scrollHeight;
    }
    document.addEventListener('livewire:navigated', scrollChatToBottom);
    document.addEventListener('DOMContentLoaded', scrollChatToBottom);
    Livewire.on('mensagem-enviada', () => { setTimeout(scrollChatToBottom, 80); });
    const observer = new MutationObserver(() => {
        const el = document.getElementById('nx-chat-messages');
        if (el) { const isAtBottom = el.scrollHeight - el.clientHeight <= el.scrollTop + 80; if (isAtBottom) scrollChatToBottom(); }
    });
    const chatEl = document.getElementById('nx-chat-messages');
    if (chatEl) observer.observe(chatEl, { childList: true, subtree: true });
</script>
@endpush

@push('styles')
<style>
    .nx-chat-msg-avatar--ia { background: linear-gradient(135deg, #6366F1, #8B5CF6) !important; color: #fff !important; }
    .nx-chat-bubble--ia { background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(139,92,246,0.08)) !important; border: 1px solid rgba(99,102,241,0.18) !important; }
    .nx-chat-ia-tag { display:inline-flex;align-items:center;padding:1px 6px;font-size:10px;font-weight:700;border-radius:4px;background:linear-gradient(135deg,#6366F1,#8B5CF6);color:#fff;letter-spacing:.05em;margin-left:3px; }
    .nx-ia-typing-dots { display:inline-flex;align-items:center;gap:4px; }
    .nx-ia-typing-dots span { display:inline-block;width:7px;height:7px;border-radius:50%;background:#6366F1;animation:nx-bounce 1.4s infinite ease-in-out; }
    .nx-ia-typing-dots span:nth-child(2) { animation-delay:.2s; }
    .nx-ia-typing-dots span:nth-child(3) { animation-delay:.4s; }
    @keyframes nx-bounce { 0%,80%,100% { transform:scale(0.7);opacity:.5; } 40% { transform:scale(1);opacity:1; } }
    .nx-chat-ticket-item-wrap { position: relative; display: flex; align-items: stretch; }
    .nx-chat-ticket-item-wrap .nx-chat-ticket-item { flex: 1; }
    .nx-chat-ticket-delete { position: absolute; right: 10px; top: 12px; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; border: 1px solid rgba(226,232,240,0.9); background: #fff; color: #ef4444; cursor: pointer; }
    .nx-chat-ticket-delete:hover { background: #fee2e2; border-color: #fecaca; }
</style>
@endpush
