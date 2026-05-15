<div class="nx-nfe-page">

    {{-- ── TOPBAR ───────────────────────────────────────────────── --}}
    <div class="nx-nfe-topbar">
        <div class="nx-nfe-topbar-left">
            <button type="button" onclick="window.close()" class="nx-nfe-back-btn" title="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="nx-nfe-topbar-info">
                <div class="nx-nfe-topbar-title">
                    NF-e #{{ $fiscalNote->invoice_number }} / Série {{ $fiscalNote->series }}
                    <span class="nx-so-badge {{ $fiscalNote->status->badgeClass() }}">{{ $fiscalNote->status->label() }}</span>
                </div>
                <div class="nx-nfe-topbar-sub">
                    Cliente: {{ $fiscalNote->client->name ?? 'N/A' }} |
                    Valor: R$ {{ number_format($fiscalNote->amount, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="nx-nfe-topbar-actions">
            @if($fiscalNote->status === \App\Enums\FiscalNoteStatus::Draft)
                <button type="button" wire:click="transmitir" wire:loading.attr="disabled"
                    class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="transmitir">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Transmitir SEFAZ
                    </span>
                    <span wire:loading wire:target="transmitir">Transmitindo…</span>
                </button>
            @endif

            @if($fiscalNote->status === \App\Enums\FiscalNoteStatus::Authorized)
                <button type="button" wire:click="openEventsModal" class="nx-btn nx-btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Eventos
                </button>
                <a href="{{ route('api.nfe.danfe', $fiscalNote->id) }}" target="_blank" class="nx-btn nx-btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    DANFE
                </a>
            @endif
        </div>
    </div>

    {{-- ── FLASH ─────────────────────────────────────────────────── --}}
    @session('success')
        <div class="nx-nfe-alert nx-nfe-alert--success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="nx-nfe-alert nx-nfe-alert--error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,8000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ── BODY ──────────────────────────────────────────────────── --}}
    <div class="nx-nfe-body">

        {{-- Coluna Esquerda: Preview DANFE --}}
        <div class="nx-nfe-preview">
            @if($fiscalNote->status === \App\Enums\FiscalNoteStatus::Draft)
                <div class="nx-nfe-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:#F59E0B"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <h3>⚠️ Nota Fiscal SEM VALOR FISCAL</h3>
                    <p>Esta NF-e ainda NÃO foi transmitida para a SEFAZ.</p>
                    <p>Este documento é apenas uma pré-visualização e não possui validade fiscal.</p>
                    <p><strong>Transmita a nota</strong> para que ela seja autorizada e tenha valor legal.</p>
                </div>
            @endif

            <div class="nx-nfe-iframe-container">
                <iframe src="{{ route('api.nfe.danfe.visualizar', $fiscalNote->id) }}"
                    frameborder="0"
                    class="nx-nfe-iframe"
                    title="Pré-visualização DANFE"></iframe>
            </div>
        </div>

        {{-- Coluna Direita: Informações e Ações --}}
        <div class="nx-nfe-sidebar">

            {{-- Card Status --}}
            <div class="nx-nfe-card">
                <div class="nx-nfe-card-title">Status da NF-e</div>
                <div class="nx-nfe-status-box">
                    <div class="nx-nfe-status-icon {{ $fiscalNote->status === \App\Enums\FiscalNoteStatus::Authorized ? 'nx-nfe-status-icon--success' : 'nx-nfe-status-icon--warning' }}">
                        @if($fiscalNote->status === \App\Enums\FiscalNoteStatus::Authorized)
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="nx-nfe-status-label">{{ $fiscalNote->status->label() }}</div>
                        @if($fiscalNote->protocol)
                            <div class="nx-nfe-status-detail">Protocolo: {{ $fiscalNote->protocol }}</div>
                        @endif
                        @if($fiscalNote->access_key)
                            <div class="nx-nfe-status-detail">Chave: {{ chunk_split($fiscalNote->access_key, 4, ' ') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Dados --}}
            <div class="nx-nfe-card">
                <div class="nx-nfe-card-title">Dados da Nota</div>
                <div class="nx-nfe-data-row">
                    <span>Número</span>
                    <strong>{{ $fiscalNote->invoice_number }}</strong>
                </div>
                <div class="nx-nfe-data-row">
                    <span>Série</span>
                    <strong>{{ $fiscalNote->series }}</strong>
                </div>
                <div class="nx-nfe-data-row">
                    <span>Cliente</span>
                    <strong>{{ $fiscalNote->client->name ?? 'N/A' }}</strong>
                </div>
                <div class="nx-nfe-data-row">
                    <span>CNPJ/CPF</span>
                    <strong>{{ $fiscalNote->client->taxNumber ?? 'N/A' }}</strong>
                </div>
                <div class="nx-nfe-data-row">
                    <span>Valor Total</span>
                    <strong style="color:#10B981;font-size:16px">R$ {{ number_format($fiscalNote->amount, 2, ',', '.') }}</strong>
                </div>
                @if($fiscalNote->authorized_at)
                <div class="nx-nfe-data-row">
                    <span>Autorizada em</span>
                    <strong>{{ $fiscalNote->authorized_at->format('d/m/Y H:i') }}</strong>
                </div>
                @endif
            </div>

            {{-- Card Ações (apenas se autorizada) --}}
            @if($fiscalNote->status === \App\Enums\FiscalNoteStatus::Authorized)
            <div class="nx-nfe-card">
                <div class="nx-nfe-card-title">Ações</div>
                <div class="nx-nfe-actions">
                    <button type="button" wire:click="openCancelModal" class="nx-nfe-action-btn nx-nfe-action-btn--danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Cancelar NF-e
                    </button>
                    <button type="button" wire:click="openCceModal" class="nx-nfe-action-btn nx-nfe-action-btn--warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Carta de Correção
                    </button>
                    <button type="button" wire:click="openEventsModal" class="nx-nfe-action-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Ver Eventos
                    </button>
                </div>
            </div>
            @endif

            {{-- Card Pedido Relacionado --}}
            @if($fiscalNote->salesOrder)
            <div class="nx-nfe-card">
                <div class="nx-nfe-card-title">Pedido Relacionado</div>
                <div class="nx-nfe-data-row">
                    <span>Pedido</span>
                    <strong>#{{ $fiscalNote->salesOrder->order_number }}</strong>
                </div>
                <div class="nx-nfe-data-row">
                    <span>Status</span>
                    <span class="nx-so-badge {{ $fiscalNote->salesOrder->status->badgeClass() }}" style="font-size:11px">
                        {{ $fiscalNote->salesOrder->status->label() }}
                    </span>
                </div>
                <a href="{{ route('vendas.pedidos.editar', $fiscalNote->salesOrder->id) }}" target="_blank" class="nx-nfe-link">
                    Ver Pedido →
                </a>
            </div>
            @endif

        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: EVENTOS
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($showEventsModal)
    <div class="nx-nfe-modal-overlay" wire:click.self="closeEventsModal">
        <div class="nx-nfe-modal">
            <div class="nx-nfe-modal-header">
                <h3>Eventos da NF-e</h3>
                <button type="button" wire:click="closeEventsModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-nfe-modal-body">
                @if($fiscalNote->events->isEmpty())
                    <p style="color:#94A3B8;text-align:center;padding:20px">Nenhum evento registrado.</p>
                @else
                    <div class="nx-nfe-events">
                        @foreach($fiscalNote->events as $event)
                        <div class="nx-nfe-event-item">
                            <div class="nx-nfe-event-dot"></div>
                            <div class="nx-nfe-event-content">
                                <div class="nx-nfe-event-type">{{ $event->event_type }}</div>
                                <div class="nx-nfe-event-desc">{{ $event->description }}</div>
                                @if($event->protocol)
                                    <div class="nx-nfe-event-protocol">Protocolo: {{ $event->protocol }}</div>
                                @endif
                                <div class="nx-nfe-event-time">{{ $event->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CANCELAR NF-e
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($showCancelModal)
    <div class="nx-nfe-modal-overlay" wire:click.self="closeCancelModal">
        <div class="nx-nfe-modal">
            <div class="nx-nfe-modal-header">
                <h3>Cancelar NF-e</h3>
                <button type="button" wire:click="closeCancelModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-nfe-modal-body">
                <div class="nx-nfe-warning" style="margin-bottom:16px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:#EF4444"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <div>
                        <h4>Atenção!</h4>
                        <p>O cancelamento é irreversível e será comunicado à SEFAZ.</p>
                    </div>
                </div>
                <div class="nx-field">
                    <label>Motivo do Cancelamento <span style="color:#DC2626">*</span></label>
                    <textarea wire:model="cancelReason" rows="4"
                        placeholder="Informe o motivo do cancelamento (mínimo 15 caracteres)…"></textarea>
                    @error('cancelReason') <small style="color:#DC2626;font-size:12px">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="nx-nfe-modal-footer">
                <button type="button" wire:click="closeCancelModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="cancelar" class="nx-btn" style="background:#DC2626;color:#fff">
                    Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CARTA DE CORREÇÃO
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($showCceModal)
    <div class="nx-nfe-modal-overlay" wire:click.self="closeCceModal">
        <div class="nx-nfe-modal">
            <div class="nx-nfe-modal-header">
                <h3>Carta de Correção Eletrônica (CC-e)</h3>
                <button type="button" wire:click="closeCceModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nx-nfe-modal-body">
                <p style="font-size:13px;color:#64748B;margin-bottom:16px">
                    A CC-e permite corrigir informações <strong>não fiscais</strong> como endereço, transportadora, dados cadastrais, etc.
                </p>
                <div class="nx-field">
                    <label>Texto da Correção <span style="color:#DC2626">*</span></label>
                    <textarea wire:model="cceText" rows="6"
                        placeholder="Descreva a correção a ser feita (mínimo 15 caracteres)…"></textarea>
                    @error('cceText') <small style="color:#DC2626;font-size:12px">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="nx-nfe-modal-footer">
                <button type="button" wire:click="closeCceModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="enviarCce" class="nx-btn nx-btn-primary">
                    Enviar CC-e
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
/* ═══════════════════════════════════════════
   NF-e GESTÃO PAGE
═══════════════════════════════════════════ */
.nx-nfe-page { display:flex; flex-direction:column; height:100vh; background:#F1F5F9; overflow:hidden; }

/* Topbar */
.nx-nfe-topbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 20px; background:#fff; border-bottom:1px solid #E2E8F0;
    flex-shrink:0;
}
.nx-nfe-topbar-left { display:flex; align-items:center; gap:12px; }
.nx-nfe-back-btn {
    display:flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:8px; border:1px solid #E2E8F0;
    color:#64748B; background:#fff; cursor:pointer; transition:all .15s;
}
.nx-nfe-back-btn:hover { background:#FEE2E2; border-color:#FECACA; color:#DC2626; }
.nx-nfe-topbar-info { display:flex; flex-direction:column; gap:2px; }
.nx-nfe-topbar-title { font-size:15px; font-weight:700; color:#0F172A; display:flex; align-items:center; gap:8px; }
.nx-nfe-topbar-sub { font-size:12px; color:#64748B; }
.nx-nfe-topbar-actions { display:flex; align-items:center; gap:8px; }

/* Alerts */
.nx-nfe-alert {
    display:flex; align-items:center; gap:8px; padding:10px 20px;
    font-size:13px; border-bottom:1px solid transparent; flex-shrink:0;
}
.nx-nfe-alert--success { background:#F0FDF4; border-color:#BBF7D0; color:#15803D; }
.nx-nfe-alert--error   { background:#FEF2F2; border-color:#FECACA; color:#DC2626; }

/* Body */
.nx-nfe-body { display:flex; flex:1; min-height:0; gap:0; }

/* Preview (esquerda) */
.nx-nfe-preview {
    flex:1; display:flex; flex-direction:column; background:#E2E8F0;
    padding:16px; overflow:hidden;
}
.nx-nfe-warning {
    background:#FFFBEB; border:2px solid #FCD34D; border-radius:12px;
    padding:20px; text-align:center; margin-bottom:16px;
}
.nx-nfe-warning h3 { font-size:16px; font-weight:700; color:#92400E; margin:12px 0 8px; }
.nx-nfe-warning p { font-size:13px; color:#78350F; margin:4px 0; }
.nx-nfe-iframe-container {
    flex:1; background:#fff; border:1px solid #CBD5E1; border-radius:12px;
    overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);
}
.nx-nfe-iframe { width:100%; height:100%; }

/* Sidebar (direita) */
.nx-nfe-sidebar {
    width:340px; background:#fff; border-left:1px solid#E2E8F0;
    padding:16px; overflow-y:auto; flex-shrink:0;
}
.nx-nfe-card {
    background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
    padding:14px; margin-bottom:12px;
}
.nx-nfe-card-title {
    font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase;
    letter-spacing:.05em; margin-bottom:12px;
}

/* Status Box */
.nx-nfe-status-box {
    display:flex; align-items:flex-start; gap:12px;
}
.nx-nfe-status-icon {
    width:48px; height:48px; border-radius:10px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.nx-nfe-status-icon--success { background:#ECFDF5; color:#10B981; }
.nx-nfe-status-icon--warning { background:#FEF3C7; color:#F59E0B; }
.nx-nfe-status-label { font-size:15px; font-weight:700; color:#0F172A; }
.nx-nfe-status-detail { font-size:11px; color:#64748B; margin-top:4px; word-break:break-all; }

/* Data Rows */
.nx-nfe-data-row {
    display:flex; justify-content:space-between; align-items:center;
    padding:8px 0; border-bottom:1px solid #F1F5F9; font-size:13px;
}
.nx-nfe-data-row:last-child { border-bottom:none; }
.nx-nfe-data-row span { color:#64748B; }
.nx-nfe-data-row strong { color:#0F172A; }

/* Actions */
.nx-nfe-actions { display:flex; flex-direction:column; gap:8px; }
.nx-nfe-action-btn {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:10px 14px; border-radius:8px; border:1px solid #E2E8F0;
    background:#fff; color:#475569; font-size:13px; font-weight:600;
    cursor:pointer; transition:all .15s; width:100%;
}
.nx-nfe-action-btn:hover { border-color:#CBD5E1; background:#F8FAFC; }
.nx-nfe-action-btn--danger { color:#DC2626; }
.nx-nfe-action-btn--danger:hover { background:#FEF2F2; border-color:#FECACA; }
.nx-nfe-action-btn--warning { color:#D97706; }
.nx-nfe-action-btn--warning:hover { background:#FFFBEB; border-color:#FCD34D; }
.nx-nfe-link {
    display:inline-flex; align-items:center; gap:4px; font-size:13px;
    color:#4F46E5; font-weight:600; text-decoration:none; margin-top:8px;
}
.nx-nfe-link:hover { text-decoration:underline; }

/* Modal */
.nx-nfe-modal-overlay {
    position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:100;
    display:flex; align-items:center; justify-content:center; padding:20px;
}
.nx-nfe-modal {
    width:100%; max-width:600px; background:#fff; border-radius:12px;
    box-shadow:0 20px 60px rgba(0,0,0,0.2); display:flex; flex-direction:column;
    max-height:90vh;
}
.nx-nfe-modal-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:20px 24px; border-bottom:1px solid #E2E8F0;
}
.nx-nfe-modal-header h3 { font-size:16px; font-weight:700; color:#0F172A; }
.nx-nfe-modal-body { padding:24px; overflow-y:auto; flex:1; }
.nx-nfe-modal-footer {
    display:flex; align-items:center; justify-content:flex-end; gap:8px;
    padding:16px 24px; border-top:1px solid #E2E8F0;
}

/* Events Timeline */
.nx-nfe-events { display:flex; flex-direction:column; gap:0; }
.nx-nfe-event-item {
    display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #F1F5F9;
}
.nx-nfe-event-item:last-child { border-bottom:none; }
.nx-nfe-event-dot {
    width:10px; height:10px; border-radius:50%; background:#818CF8;
    flex-shrink:0; margin-top:4px;
}
.nx-nfe-event-content { flex:1; }
.nx-nfe-event-type {
    font-size:12px; font-weight:700; color:#6366F1;
    text-transform:uppercase; letter-spacing:.05em;
}
.nx-nfe-event-desc { font-size:13px; color:#0F172A; margin-top:4px; }
.nx-nfe-event-protocol { font-size:11px; color:#64748B; margin-top:4px; }
.nx-nfe-event-time { font-size:11px; color:#94A3B8; margin-top:4px; }

/* Field */
.nx-field { display:flex; flex-direction:column; gap:6px; }
.nx-field label { font-size:13px; font-weight:500; color:#374151; }
.nx-field textarea {
    padding:10px 12px; border:1px solid #E2E8F0; border-radius:8px;
    font-size:13px; color:#0F172A; font-family:inherit; resize:vertical;
}
.nx-field textarea:focus { outline:none; border-color:#818CF8; }
</style>
@endpush

