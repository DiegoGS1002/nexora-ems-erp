<div class="nx-nfe-page">

    {{-- ══════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'fiscal') }}" class="nx-breadcrumb-link">Fiscal</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">Notas Fiscais</span>
            </nav>
            <h1 class="nx-page-title">Notas Fiscais Eletrônicas</h1>
            <p class="nx-page-subtitle">Monitoramento, transmissão e controle de documentos fiscais eletrônicos.</p>
        </div>
        <div class="nx-page-actions">
            {{-- Monitor de Status SEFAZ --}}
            <div class="nx-nfe-sefaz-monitor">
                <div class="nx-nfe-sefaz-dot"></div>
                <div class="nx-nfe-sefaz-info">
                    <span class="nx-nfe-sefaz-label">SEFAZ</span>
                    <span class="nx-nfe-sefaz-status">Online</span>
                </div>
                <button type="button" class="nx-nfe-sefaz-sync" title="Sincronizar status">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                </button>
            </div>
            <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Nota
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="alert-error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,7000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ $value }}
        </div>
    @endsession

    {{-- ══════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════ --}}
    <div class="nx-nfe-kpis">

        {{-- Total Emitidas --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total Emitidas</p>
                    <p class="nx-kpi-card-value" style="color:#1D4ED8;">{{ $kpis['total'] }}</p>
                    <span class="nx-kpi-card-trend">Todos os status</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;border-color:rgba(59,130,246,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
            </div>
        </div>

        {{-- Autorizadas --}}
        <div class="nx-kpi-card" style="border-left:3px solid #10B981;">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Autorizadas</p>
                    <p class="nx-kpi-card-value" style="color:#15803D;font-size:22px;">{{ $kpis['authorized'] }}</p>
                    <span class="nx-kpi-card-trend is-positive">Aprovadas pela SEFAZ</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;border-color:rgba(16,185,129,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>

        {{-- Rejeitadas --}}
        <div class="nx-kpi-card" style="border-left:3px solid #EF4444;">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Rejeitadas</p>
                    <p class="nx-kpi-card-value" style="color:#B91C1C;font-size:22px;">{{ $kpis['rejected'] }}</p>
                    <span class="nx-kpi-card-trend is-negative">Requer correção</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;border-color:rgba(239,68,68,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>

        {{-- Valor Total Autorizado --}}
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Valor Autorizado</p>
                    <p class="nx-kpi-card-value" style="color:#0F172A;font-size:20px;">
                        R$ {{ number_format($kpis['totalValue'], 2, ',', '.') }}
                    </p>
                    <span class="nx-kpi-card-trend is-positive">Notas autorizadas</span>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.1);color:#6366F1;border-color:rgba(99,102,241,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         FILTER TOOLBAR
    ══════════════════════════════════════════════════ --}}
    <div class="nx-nfe-toolbar">

        {{-- Status Tabs --}}
        <div class="nx-nfe-tabs">
            <button type="button" wire:click="$set('filterStatus','')" class="nx-nfe-tab {{ $filterStatus === '' ? 'nx-nfe-tab--active' : '' }}">Todos</button>
            <button type="button" wire:click="$set('filterStatus','draft')" class="nx-nfe-tab {{ $filterStatus === 'draft' ? 'nx-nfe-tab--active' : '' }}">
                <span class="nx-nfe-tab-dot nx-dot-nfe-draft"></span> Rascunho
            </button>
            <button type="button" wire:click="$set('filterStatus','sent')" class="nx-nfe-tab {{ $filterStatus === 'sent' ? 'nx-nfe-tab--active nx-nfe-tab--sent' : '' }}">
                <span class="nx-nfe-tab-dot nx-dot-nfe-sent"></span> Enviadas
            </button>
            <button type="button" wire:click="$set('filterStatus','authorized')" class="nx-nfe-tab {{ $filterStatus === 'authorized' ? 'nx-nfe-tab--active nx-nfe-tab--authorized' : '' }}">
                <span class="nx-nfe-tab-dot nx-dot-nfe-authorized"></span> Autorizadas
            </button>
            <button type="button" wire:click="$set('filterStatus','rejected')" class="nx-nfe-tab {{ $filterStatus === 'rejected' ? 'nx-nfe-tab--active nx-nfe-tab--rejected' : '' }}">
                <span class="nx-nfe-tab-dot nx-dot-nfe-rejected"></span> Rejeitadas
            </button>
            <button type="button" wire:click="$set('filterStatus','cancelled')" class="nx-nfe-tab {{ $filterStatus === 'cancelled' ? 'nx-nfe-tab--active nx-nfe-tab--cancelled' : '' }}">
                <span class="nx-nfe-tab-dot nx-dot-nfe-cancelled"></span> Canceladas
            </button>
        </div>

        {{-- Right Filters --}}
        <div class="nx-filters-bar" style="margin-top:0;flex:1;justify-content:flex-end;">
            {{-- Type --}}
            <select wire:model.live="filterType" class="nx-filter-select" title="Tipo de documento">
                <option value="">Todos os Tipos</option>
                <option value="nfe">NF-e</option>
                <option value="nfce">NFC-e</option>
            </select>
            {{-- Environment --}}
            <select wire:model.live="filterEnv" class="nx-filter-select" title="Ambiente">
                <option value="">Prod. e Homol.</option>
                <option value="production">Produção</option>
                <option value="homologation">Homologação</option>
            </select>
            {{-- Search --}}
            <div class="nx-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" wire:model.live.debounce.400ms="search" class="nx-search" placeholder="Buscar por número, cliente, chave…">
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════ --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Nº / Série</th>
                        <th>Destinatário</th>
                        <th>Chave de Acesso</th>
                        <th class="nx-th-center">Tipo / Amb.</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-right">Valor</th>
                        <th>Data</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr>
                            {{-- Número / Série --}}
                            <td>
                                <div class="nx-nfe-num-cell">
                                    <span class="nx-nfe-num">{{ $note->invoice_number }}</span>
                                    <span class="nx-nfe-serie">Série {{ $note->series }}</span>
                                </div>
                            </td>

                            {{-- Destinatário --}}
                            <td>
                                <div class="nx-nfe-client-cell">
                                    <span class="nx-nfe-client-name">{{ $note->display_client }}</span>
                                    @if($note->client)
                                        <span class="nx-nfe-client-doc">{{ $note->client->taxNumber ?? '—' }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Chave de Acesso --}}
                            <td>
                                @if($note->access_key)
                                    <span class="nx-nfe-access-key" title="{{ $note->access_key }}">
                                        {{ substr($note->access_key, 0, 11) }}…{{ substr($note->access_key, -4) }}
                                    </span>
                                @else
                                    <span class="nx-td-muted">—</span>
                                @endif
                            </td>

                            {{-- Tipo / Ambiente --}}
                            <td class="nx-td-center">
                                <div style="display:flex;flex-direction:column;gap:4px;align-items:center;">
                                    <span class="nx-nfe-type-badge nx-nfe-type-{{ $note->type }}">
                                        {{ strtoupper($note->type) }}
                                    </span>
                                    <span class="nx-nfe-env-badge nx-nfe-env-{{ $note->environment }}">
                                        {{ $note->environment === 'production' ? 'Produção' : 'Homol.' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="nx-td-center">
                                <span class="nx-badge {{ $note->status->badgeClass() }}">
                                    {{ $note->status->label() }}
                                </span>
                            </td>

                            {{-- Valor --}}
                            <td class="nx-td-right">
                                <span class="nx-nfe-amount">R$ {{ number_format($note->amount, 2, ',', '.') }}</span>
                            </td>

                            {{-- Data --}}
                            <td>
                                <div class="nx-nfe-date-cell">
                                    <span>{{ $note->created_at->format('d/m/Y') }}</span>
                                    <span class="nx-nfe-date-time">{{ $note->created_at->format('H:i') }}</span>
                                </div>
                            </td>

                            {{-- Ações --}}
                            <td class="nx-td-actions">
                                <div class="nx-nfe-actions" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" class="nx-action-btn" @click="open = !open" title="Ações">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                    </button>

                                    <div class="nx-nfe-dropdown" x-show="open" x-transition x-cloak>

                                        {{-- Visualizar --}}
                                        <button type="button" wire:click="openView({{ $note->id }})" @click="open=false" class="nx-nfe-dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Visualizar
                                        </button>

                                        {{-- Imprimir DANFE --}}
                                        @if($note->status === \App\Enums\FiscalNoteStatus::Authorized)
                                            <button type="button" @click="open=false" class="nx-nfe-dropdown-item nx-dropdown-danfe">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                                Imprimir DANFE
                                            </button>
                                        @endif

                                        {{-- Baixar XML --}}
                                        @if($note->hasXml())
                                            <button type="button" @click="open=false" class="nx-nfe-dropdown-item nx-dropdown-xml">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                Baixar XML
                                            </button>
                                        @endif

                                        {{-- Editar (só rascunho) --}}
                                        @if($note->isEditable())
                                            <button type="button" wire:click="openEdit({{ $note->id }})" @click="open=false" class="nx-nfe-dropdown-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                Editar
                                            </button>
                                        @endif

                                        {{-- Cancelar (só autorizada) --}}
                                        @if($note->isCancellable())
                                            <div class="nx-nfe-dropdown-divider"></div>
                                            <button type="button" wire:click="openCancel({{ $note->id }})" @click="open=false" class="nx-nfe-dropdown-item nx-dropdown-cancel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                Cancelar Nota
                                            </button>
                                        @endif

                                        {{-- Excluir --}}
                                        @if($note->isEditable())
                                            <button type="button" wire:click="confirmDelete({{ $note->id }})" @click="open=false" class="nx-nfe-dropdown-item nx-dropdown-delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                                Excluir
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="nx-empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="color:#CBD5E1;margin-bottom:16px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p class="nx-empty-title">Nenhuma nota fiscal encontrada</p>
                                    <p class="nx-empty-desc">
                                        @if($search || $filterStatus || $filterType || $filterEnv)
                                            Tente ajustar os filtros de busca.
                                        @else
                                            Cadastre a primeira nota fiscal clicando em <strong>Nova Nota</strong>.
                                        @endif
                                    </p>
                                    @if(!$search && !$filterStatus && !$filterType && !$filterEnv)
                                        <button type="button" wire:click="openCreate" class="nx-btn nx-btn-primary nx-btn-sm">
                                            + Nova Nota
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        @if($notes->hasPages())
            <div class="nx-table-footer">
                <span class="nx-table-count">
                    Exibindo {{ $notes->firstItem() }}–{{ $notes->lastItem() }} de {{ $notes->total() }} registros
                </span>
                <div class="nx-pagination">{{ $notes->links() }}</div>
            </div>
        @else
            <div class="nx-table-footer">
                <span class="nx-table-count">{{ $notes->total() }} registro(s)</span>
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════
         MODAL — CRIAR / EDITAR NOTA FISCAL
    ══════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-modal-overlay" wire:click.self="closeModal">
        <div class="nx-modal nx-modal--lg" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">{{ $isEditing ? 'Editar Nota Fiscal' : 'Nova Nota Fiscal' }}</h2>
                        <p class="nx-modal-subtitle">{{ $isEditing ? 'Atualize os dados do documento fiscal.' : 'Cadastre um novo documento fiscal eletrônico.' }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="nx-modal-close" aria-label="Fechar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-nfe-form-grid">

                    {{-- ── Seção: Identificação ── --}}
                    <div class="nx-nfe-form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        Identificação do Documento
                    </div>

                    {{-- Número da Nota --}}
                    <div class="nx-field">
                        <label>Número <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form_invoice_number" placeholder="000001" maxlength="9">
                        @error('form_invoice_number') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Série --}}
                    <div class="nx-field">
                        <label>Série <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form_series" placeholder="1" maxlength="3">
                        @error('form_series') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="nx-field">
                        <label>Tipo <span class="nx-required">*</span></label>
                        <select wire:model="form_type">
                            <option value="nfe">NF-e (Nota Fiscal Eletrônica)</option>
                            <option value="nfce">NFC-e (Nota Fiscal ao Consumidor)</option>
                        </select>
                        @error('form_type') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Ambiente --}}
                    <div class="nx-field">
                        <label>Ambiente <span class="nx-required">*</span></label>
                        <select wire:model="form_environment">
                            <option value="homologation">Homologação (Testes)</option>
                            <option value="production">Produção</option>
                        </select>
                        @error('form_environment') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- ── Seção: Destinatário ── --}}
                    <div class="nx-nfe-form-section-title nx-nfe-form-section-title--full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Destinatário
                    </div>

                    {{-- Cliente (select) --}}
                    <div class="nx-field nx-nfe-field--half">
                        <label>Cliente Cadastrado</label>
                        <select wire:model.live="form_client_id">
                            <option value="">— Selecionar cliente —</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected($form_client_id == $client->id)>
                                    {{ $client->social_name ?? $client->name }}
                                </option>
                            @endforeach
                        </select>
                        <small>Ou informe o nome manualmente abaixo.</small>
                    </div>

                    {{-- Nome do Cliente --}}
                    <div class="nx-field nx-nfe-field--half">
                        <label>Nome / Razão Social</label>
                        <input type="text" wire:model="form_client_name" placeholder="Destinatário da nota" autocomplete="off">
                        @error('form_client_name') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- ── Seção: Valores e Status ── --}}
                    <div class="nx-nfe-form-section-title nx-nfe-form-section-title--full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Valores e Status
                    </div>

                    {{-- Valor Total --}}
                    <div class="nx-field">
                        <label>Valor Total (R$) <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form_amount" placeholder="0,00" inputmode="decimal">
                        @error('form_amount') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="nx-field">
                        <label>Status <span class="nx-required">*</span></label>
                        <select wire:model="form_status">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" @selected($form_status === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('form_status') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- ── Seção: Dados SEFAZ ── --}}
                    <div class="nx-nfe-form-section-title nx-nfe-form-section-title--full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Dados SEFAZ
                    </div>

                    {{-- Chave de Acesso --}}
                    <div class="nx-field nx-nfe-field--full">
                        <label>Chave de Acesso (44 dígitos)</label>
                        <input type="text" wire:model="form_access_key" placeholder="00000000000000000000000000000000000000000000" maxlength="44" style="font-family:monospace;letter-spacing:0.05em;">
                        @error('form_access_key') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Protocolo --}}
                    <div class="nx-field">
                        <label>Protocolo de Autorização</label>
                        <input type="text" wire:model="form_protocol" placeholder="Ex: 135240000000000">
                        @error('form_protocol') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Mensagem SEFAZ --}}
                    <div class="nx-field">
                        <label>Mensagem de Retorno SEFAZ</label>
                        <input type="text" wire:model="form_sefaz_message" placeholder="Ex: Autorizado o uso da NF-e">
                        @error('form_sefaz_message') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    {{-- Observações --}}
                    <div class="nx-field nx-nfe-field--full">
                        <label>Observações Internas</label>
                        <textarea wire:model="form_notes" rows="2" placeholder="Informações adicionais para controle interno…"></textarea>
                        @error('form_notes') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Salvar Alterações' : 'Cadastrar Nota' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — VISUALIZAR NOTA
    ══════════════════════════════════════════════════ --}}
    @if($showViewModal && $viewingNote)
    <div class="nx-modal-overlay" wire:click.self="closeView">
        <div class="nx-modal nx-modal--lg" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(99,102,241,0.1);color:#6366F1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title">Detalhes da Nota Fiscal</h2>
                        <p class="nx-modal-subtitle">NF-e nº {{ $viewingNote->invoice_number }} — Série {{ $viewingNote->series }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeView" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">

                {{-- Status Banner --}}
                <div class="nx-nfe-status-banner nx-nfe-banner-{{ $viewingNote->status->value }}">
                    <span class="nx-badge {{ $viewingNote->status->badgeClass() }}" style="font-size:13px;padding:5px 14px;">
                        {{ $viewingNote->status->label() }}
                    </span>
                    @if($viewingNote->authorized_at)
                        <span class="nx-nfe-banner-meta">Autorizada em {{ $viewingNote->authorized_at->format('d/m/Y \à\s H:i') }}</span>
                    @endif
                    @if($viewingNote->cancelled_at)
                        <span class="nx-nfe-banner-meta">Cancelada em {{ $viewingNote->cancelled_at->format('d/m/Y \à\s H:i') }}</span>
                    @endif
                </div>

                {{-- Grid de detalhes --}}
                <div class="nx-nfe-detail-grid">
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Número</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->invoice_number }}</span>
                    </div>
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Série</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->series }}</span>
                    </div>
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Tipo</span>
                        <span class="nx-nfe-detail-value">{{ strtoupper($viewingNote->type) }}</span>
                    </div>
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Ambiente</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->environment === 'production' ? 'Produção' : 'Homologação' }}</span>
                    </div>
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Destinatário</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->display_client }}</span>
                    </div>
                    <div class="nx-nfe-detail-item">
                        <span class="nx-nfe-detail-label">Valor Total</span>
                        <span class="nx-nfe-detail-value" style="color:#15803D;font-weight:700;">
                            R$ {{ number_format($viewingNote->amount, 2, ',', '.') }}
                        </span>
                    </div>
                    @if($viewingNote->protocol)
                    <div class="nx-nfe-detail-item nx-nfe-detail-item--full">
                        <span class="nx-nfe-detail-label">Protocolo de Autorização</span>
                        <span class="nx-nfe-detail-value" style="font-family:monospace;">{{ $viewingNote->protocol }}</span>
                    </div>
                    @endif
                    @if($viewingNote->access_key)
                    <div class="nx-nfe-detail-item nx-nfe-detail-item--full">
                        <span class="nx-nfe-detail-label">Chave de Acesso</span>
                        <span class="nx-nfe-access-key-full">{{ $viewingNote->formatted_access_key }}</span>
                    </div>
                    @endif
                    @if($viewingNote->sefaz_message)
                    <div class="nx-nfe-detail-item nx-nfe-detail-item--full">
                        <span class="nx-nfe-detail-label">Mensagem SEFAZ</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->sefaz_message }}</span>
                    </div>
                    @endif
                    @if($viewingNote->cancel_reason)
                    <div class="nx-nfe-detail-item nx-nfe-detail-item--full">
                        <span class="nx-nfe-detail-label" style="color:#B91C1C;">Motivo do Cancelamento</span>
                        <span class="nx-nfe-detail-value" style="color:#B91C1C;">{{ $viewingNote->cancel_reason }}</span>
                    </div>
                    @endif
                    @if($viewingNote->notes)
                    <div class="nx-nfe-detail-item nx-nfe-detail-item--full">
                        <span class="nx-nfe-detail-label">Observações</span>
                        <span class="nx-nfe-detail-value">{{ $viewingNote->notes }}</span>
                    </div>
                    @endif
                </div>

            </div>

            <div class="nx-modal-footer">
                @if($viewingNote->isCancellable())
                    <button type="button" wire:click="openCancel({{ $viewingNote->id }}); closeView()" class="nx-btn nx-btn-ghost" style="color:#B91C1C;border-color:#FECACA;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        Cancelar Nota
                    </button>
                @endif
                <button type="button" wire:click="closeView" class="nx-btn nx-btn-primary">Fechar</button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — CANCELAR NOTA
    ══════════════════════════════════════════════════ --}}
    @if($showCancelModal)
    <div class="nx-modal-overlay" wire:click.self="closeCancelModal">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(239,68,68,0.1);color:#EF4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#B91C1C;">Cancelar Nota Fiscal</h2>
                        <p class="nx-modal-subtitle">O cancelamento é irreversível perante a SEFAZ.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeCancelModal" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <div class="nx-nfe-cancel-notice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#D97706"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>O cancelamento só é permitido dentro do prazo legal e deve ser informado à SEFAZ.</span>
                </div>
                <div class="nx-field nx-field--full" style="margin-top:16px;">
                    <label>Motivo do Cancelamento <span class="nx-required">*</span></label>
                    <textarea wire:model="cancel_reason" rows="3"
                        placeholder="Informe o motivo (mínimo 15 caracteres). Ex: Erro na emissão da nota fiscal…"></textarea>
                    @error('cancel_reason') <small class="nx-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="closeCancelModal" class="nx-btn nx-btn-ghost">Voltar</button>
                <button type="button" wire:click="confirmCancel" wire:loading.attr="disabled" class="nx-btn nx-btn-danger">
                    <span wire:loading.remove wire:target="confirmCancel">Confirmar Cancelamento</span>
                    <span wire:loading wire:target="confirmCancel">Cancelando…</span>
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════
         MODAL — CONFIRMAR EXCLUSÃO
    ══════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
    <div class="nx-modal-overlay" wire:click.self="cancelDelete">
        <div class="nx-modal nx-modal--sm" x-data x-trap.noscroll="true">

            <div class="nx-modal-header">
                <div class="nx-modal-header-left">
                    <div class="nx-modal-icon-wrap" style="background:rgba(239,68,68,0.1);color:#EF4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </div>
                    <div>
                        <h2 class="nx-modal-title" style="color:#B91C1C;">Confirmar Exclusão</h2>
                        <p class="nx-modal-subtitle">Apenas notas em rascunho podem ser excluídas.</p>
                    </div>
                </div>
                <button type="button" wire:click="cancelDelete" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="nx-modal-body">
                <p style="font-size:14px;color:#475569;text-align:center;padding:8px 0;">
                    Tem certeza que deseja excluir esta nota fiscal? Esta ação não poderá ser desfeita.
                </p>
            </div>

            <div class="nx-modal-footer">
                <button type="button" wire:click="cancelDelete" class="nx-btn nx-btn-ghost">Cancelar</button>
                <button type="button" wire:click="delete" wire:loading.attr="disabled" class="nx-btn nx-btn-danger">
                    <span wire:loading.remove wire:target="delete">Excluir Definitivamente</span>
                    <span wire:loading wire:target="delete">Excluindo…</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>

