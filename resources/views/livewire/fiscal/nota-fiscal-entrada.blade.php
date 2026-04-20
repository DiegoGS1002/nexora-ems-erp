<div class="nx-so-page">

    {{-- ── PAGE HEADER ──────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <nav class="nx-breadcrumb">
                <a href="{{ route('home') }}" class="nx-breadcrumb-link" wire:navigate>Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'fiscal') }}" class="nx-breadcrumb-link" wire:navigate>Fiscal</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="nx-breadcrumb-current">NF de Entrada</span>
            </nav>
            <h1 class="nx-page-title">Notas Fiscais de Entrada</h1>
            <p class="nx-page-subtitle">Registro e controle de notas fiscais recebidas de fornecedores</p>
        </div>
        <div class="nx-page-actions">
            <button type="button" wire:click="openXmlModal" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                Importar XML
            </button>
            <button type="button" wire:click="openModal" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova NF Entrada
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
                    <p class="nx-kpi-card-title">Total de NFs</p>
                    <p class="nx-kpi-card-value">{{ $this->stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.08);color:#6366F1;border-color:rgba(99,102,241,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #F59E0B">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Aguard. Conferência</p>
                    <p class="nx-kpi-card-value" style="color:#F59E0B">{{ $this->stats['aguardando'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(245,158,11,0.08);color:#F59E0B;border-color:rgba(245,158,11,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #10B981">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Escrituradas</p>
                    <p class="nx-kpi-card-value" style="color:#10B981">{{ $this->stats['escriturada'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.08);color:#10B981;border-color:rgba(16,185,129,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #EF4444">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Canceladas</p>
                    <p class="nx-kpi-card-value" style="color:#EF4444">{{ $this->stats['cancelada'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.08);color:#EF4444;border-color:rgba(239,68,68,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
        </div>
        <div class="nx-kpi-card" style="border-top:3px solid #059669">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Valor Total</p>
                    <p class="nx-kpi-card-value" style="color:#059669;font-size:16px;">R$ {{ number_format($this->stats['valor_total'], 2, ',', '.') }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(5,150,105,0.08);color:#059669;border-color:rgba(5,150,105,0.18)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTERS ──────────────────────────────────── --}}
    <div class="nx-filters-bar">
        <div class="nx-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar número, fornecedor ou chave de acesso..." class="nx-search">
        </div>
        <select wire:model.live="filterStatus" class="nx-filter-select">
            <option value="">Todos os Status</option>
            <option value="digitada">Digitada</option>
            <option value="importada">Importada</option>
            <option value="validada">Validada</option>
            <option value="aguardando_conferencia">Aguard. Conferência</option>
            <option value="escriturada">Escriturada</option>
            <option value="cancelada">Cancelada</option>
        </select>
    </div>

    {{-- ── TABLE ────────────────────────────────────── --}}
    <div class="nx-card">
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Nº / Série</th>
                        <th>Fornecedor</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Emissão</th>
                        <th>Entrada</th>
                        <th class="nx-th-right">Total</th>
                        <th>Pedido Compra</th>
                        <th style="width:130px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->invoices as $inv)
                        <tr wire:key="nfe-in-{{ $inv->id }}">
                            <td>
                                <strong style="font-family:monospace;color:#6366F1;">{{ $inv->invoice_number }}</strong>
                                <span style="font-size:11px;color:#94A3B8;margin-left:4px;">/ {{ $inv->series }}</span>
                                @if($inv->access_key)
                                    <div style="font-size:10px;color:#CBD5E1;font-family:monospace;margin-top:2px;">
                                        {{ substr($inv->access_key, 0, 11) }}...
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:600;color:#1E293B;">
                                    {{ $inv->supplier?->social_name ?? $inv->supplier_name ?? '—' }}
                                </div>
                                @if($inv->supplier_cnpj)
                                    <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $inv->supplier_cnpj }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:11px;font-weight:600;padding:3px 7px;border-radius:5px;background:#F1F5F9;color:#475569;text-transform:uppercase;">
                                    {{ strtoupper($inv->doc_type) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:5px;background:{{ $inv->status_color }}18;color:{{ $inv->status_color }};border:1px solid {{ $inv->status_color }}33;">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                {{ $inv->issue_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                {{ $inv->entry_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="nx-td-right" style="font-size:13px;font-weight:600;color:#0F172A;">
                                R$ {{ number_format($inv->total_value, 2, ',', '.') }}
                            </td>
                            <td style="font-size:12px;color:#64748B;">
                                @if($inv->purchaseOrder)
                                    <span style="font-family:monospace;color:#6366F1;">{{ $inv->purchaseOrder->order_number }}</span>
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:5px;justify-content:flex-end;">
                                    <button type="button" wire:click="openDetail({{ $inv->id }})"
                                        class="nx-btn nx-btn-ghost nx-btn-sm" title="Ver detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if($inv->isEscrituravel())
                                        <button type="button"
                                            wire:click="escriturar({{ $inv->id }})"
                                            wire:confirm="Confirmar escrituração? Isso irá dar entrada no estoque e gerar conta a pagar."
                                            class="nx-btn nx-btn-sm" style="background:#10B981;color:#fff;border-color:#10B981;font-size:11px;" title="Escriturar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                            Escriturar
                                        </button>
                                    @endif
                                    @if(!in_array($inv->status, ['escriturada', 'cancelada']))
                                        <button type="button" wire:click="openEdit({{ $inv->id }})"
                                            class="nx-btn nx-btn-ghost nx-btn-sm" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button type="button"
                                            wire:click="cancel({{ $inv->id }})"
                                            wire:confirm="Cancelar esta NF de Entrada?"
                                            class="nx-btn nx-btn-ghost nx-btn-sm" title="Cancelar" style="color:#EF4444;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                @include('partials.empty-state', [
                                    'title'       => 'Nenhuma NF de Entrada encontrada',
                                    'description' => 'Importe um XML ou crie uma nota manualmente.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->invoices->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #F1F5F9;">
                {{ $this->invoices->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL — XML IMPORT
    ══════════════════════════════════════════════════════════ --}}
    @if($showXmlModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeXmlModal">
        <div class="nx-so-modal" style="max-width:500px;min-height:auto;">
            <div style="padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <h2 style="font-size:16px;font-weight:700;color:#0F172A;margin:0;">Importar XML NF-e</h2>
                        <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">Selecione o arquivo XML da NF-e para importar</p>
                    </div>
                    <button type="button" wire:click="closeXmlModal"
                        style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                @error('xmlFile')
                    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:8px;padding:10px 14px;margin-bottom:14px;color:#DC2626;font-size:13px;">
                        {{ $message }}
                    </div>
                @enderror

                <div style="border:2px dashed #CBD5E1;border-radius:12px;padding:32px;text-align:center;background:#F8FAFC;margin-bottom:20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="margin:0 auto 12px;display:block;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <p style="font-size:13px;color:#64748B;margin:0 0 12px;">Arraste o arquivo XML aqui ou clique para selecionar</p>
                    <input type="file" wire:model="xmlFile" accept=".xml" style="display:none;" id="xmlFileInput">
                    <label for="xmlFileInput" class="nx-btn nx-btn-outline" style="cursor:pointer;font-size:13px;">
                        Selecionar Arquivo XML
                    </label>
                    @if($xmlFile)
                        <p style="font-size:12px;color:#10B981;margin:8px 0 0;">
                            ✓ {{ $xmlFile->getClientOriginalName() }}
                        </p>
                    @endif
                </div>

                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:10px 14px;margin-bottom:20px;">
                    <p style="font-size:12px;color:#1D4ED8;margin:0;line-height:1.5;">
                        <strong>Formatos suportados:</strong> NF-e (modelo 55), NFC-e (modelo 65)<br>
                        O sistema irá preencher automaticamente os dados da nota, fornecedor e itens.
                    </p>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" wire:click="closeXmlModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="importXml" wire:loading.attr="disabled"
                        class="nx-btn nx-btn-primary" {{ !$xmlFile ? 'disabled' : '' }}>
                        <div wire:loading wire:target="importXml">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        </div>
                        <svg wire:loading.remove wire:target="importXml" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                        Importar XML
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MODAL — CREATE / EDIT
    ══════════════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="nx-so-modal-wrap" wire:click.self="closeModal">
        <div class="nx-so-modal">

            {{-- Modal Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 0;flex-shrink:0;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#0F172A;margin:0;">
                        {{ $editingId ? 'Editar NF de Entrada' : 'Nova NF de Entrada' }}
                    </h2>
                    <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                        {{ $status === 'importada' ? 'Dados importados via XML — revise e confirme' : 'Preencha os dados da nota fiscal recebida' }}
                    </p>
                </div>
                <button type="button" wire:click="closeModal"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="nx-so-tabs">
                @foreach([
                    ['key'=>'cabecalho','label'=>'Cabeçalho',     'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                    ['key'=>'itens',    'label'=>'Itens ('.count($invoiceItems).')','icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
                    ['key'=>'totais',  'label'=>'Totais',         'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                    ['key'=>'obs',     'label'=>'Observações',    'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                ] as $tab)
                    <button type="button" wire:click="$set('activeTab','{{ $tab['key'] }}')"
                        class="nx-so-tab {{ $activeTab === $tab['key'] ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $tab['icon'] !!}</svg>
                        {!! $tab['label'] !!}
                    </button>
                @endforeach
            </div>

            {{-- Body --}}
            <form wire:submit="save" class="nx-so-modal-body">
                @if($errors->any())
                    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:8px;padding:10px 14px;margin-bottom:14px;">
                        @foreach($errors->all() as $error)
                            <p style="font-size:12px;color:#DC2626;margin:0;">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- ── TAB: CABEÇALHO ── --}}
                <div style="{{ $activeTab !== 'cabecalho' ? 'display:none' : '' }}">
                    <div class="nx-so-section-title">Fornecedor</div>
                    <div class="nx-form-grid">
                        <div class="nx-form-group" style="grid-column:1/-1">
                            <label class="nx-label">Fornecedor (Cadastrado)</label>
                            <select wire:model="supplier_id" class="nx-input">
                                <option value="">— Selecionar ou preencher manualmente abaixo —</option>
                                @foreach($this->suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->social_name ?? $sup->name }} {{ $sup->taxNumber ? '('.$sup->taxNumber.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Nome / Razão Social <span class="nx-required">*</span></label>
                            <input type="text" wire:model="supplier_name" class="nx-input" placeholder="Razão social do fornecedor">
                            @error('supplier_name') <span class="nx-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">CNPJ</label>
                            <input type="text" wire:model="supplier_cnpj" class="nx-input" placeholder="00.000.000/0000-00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Inscrição Estadual</label>
                            <input type="text" wire:model="supplier_ie" class="nx-input" placeholder="IE">
                        </div>
                    </div>

                    <div class="nx-so-section-title" style="margin-top:16px;">Dados do Documento</div>
                    <div class="nx-form-grid">
                        <div class="nx-form-group">
                            <label class="nx-label">Número da NF <span class="nx-required">*</span></label>
                            <input type="text" wire:model="invoice_number" class="nx-input" placeholder="000000001">
                            @error('invoice_number') <span class="nx-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Série</label>
                            <input type="text" wire:model="series" class="nx-input" placeholder="1" maxlength="3">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Tipo de Documento</label>
                            <select wire:model="doc_type" class="nx-input">
                                <option value="nfe">NF-e (modelo 55)</option>
                                <option value="nfce">NFC-e (modelo 65)</option>
                                <option value="cte">CT-e</option>
                                <option value="nfse">NFS-e</option>
                            </select>
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Status</label>
                            <select wire:model="status" class="nx-input">
                                <option value="digitada">Digitada</option>
                                <option value="importada">Importada</option>
                                <option value="validada">Validada</option>
                                <option value="aguardando_conferencia">Aguardando Conferência</option>
                            </select>
                        </div>
                        <div class="nx-form-group" style="grid-column:1/-1">
                            <label class="nx-label">Chave de Acesso (44 dígitos)</label>
                            <input type="text" wire:model="access_key" class="nx-input" placeholder="Chave de acesso da NF-e" maxlength="44" style="font-family:monospace;">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Data de Emissão</label>
                            <input type="date" wire:model="issue_date" class="nx-input">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Data de Entrada <span class="nx-required">*</span></label>
                            <input type="date" wire:model="entry_date" class="nx-input">
                            @error('entry_date') <span class="nx-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">CFOP</label>
                            <input type="text" wire:model="cfop" class="nx-input" placeholder="Ex: 1102">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Natureza da Operação</label>
                            <input type="text" wire:model="operation_nature" class="nx-input" placeholder="Ex: Compra de mercadorias">
                        </div>
                        <div class="nx-form-group" style="grid-column:1/-1">
                            <label class="nx-label">Pedido de Compra (Vincular)</label>
                            <select wire:model="purchase_order_id" class="nx-input">
                                <option value="">— Nenhum —</option>
                                @foreach($this->purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->order_number }} — {{ $po->supplier?->social_name ?? $po->supplier?->name }} (R$ {{ number_format($po->total_amount, 2, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── TAB: ITENS ── --}}
                <div style="{{ $activeTab !== 'itens' ? 'display:none' : '' }}">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <span style="font-size:13px;font-weight:600;color:#475569;">{{ count($invoiceItems) }} item(ns)</span>
                        <button type="button" wire:click="addItem" class="nx-btn nx-btn-outline nx-btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Item
                        </button>
                    </div>

                    @error('invoiceItems') <div style="color:#DC2626;font-size:12px;margin-bottom:8px;">{{ $message }}</div> @enderror

                    @forelse($invoiceItems as $i => $item)
                        <div wire:key="item-{{ $i }}" style="border:1px solid #E2E8F0;border-radius:10px;padding:14px;margin-bottom:12px;background:#FAFAFA;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                <span style="font-size:12px;font-weight:700;color:#6366F1;">Item #{{ $i+1 }}</span>
                                <button type="button" wire:click="removeItem({{ $i }})"
                                    style="width:24px;height:24px;border-radius:6px;border:none;background:#FEE2E2;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#EF4444;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div class="nx-form-grid" style="grid-template-columns:repeat(3,1fr);">
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">Cód. Fornecedor</label>
                                    <input type="text" wire:model.live="invoiceItems.{{ $i }}.product_code" class="nx-input nx-input-sm" placeholder="Código do produto">
                                </div>
                                <div class="nx-form-group" style="grid-column:span 2">
                                    <label class="nx-label" style="font-size:11px;">Descrição / Produto *</label>
                                    <input type="text" wire:model.live="invoiceItems.{{ $i }}.product_name" class="nx-input nx-input-sm" placeholder="Nome do produto">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">NCM</label>
                                    <input type="text" wire:model.live="invoiceItems.{{ $i }}.ncm" class="nx-input nx-input-sm" placeholder="00000000">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">CFOP</label>
                                    <input type="text" wire:model.live="invoiceItems.{{ $i }}.cfop" class="nx-input nx-input-sm" placeholder="1102">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">Unidade</label>
                                    <input type="text" wire:model.live="invoiceItems.{{ $i }}.unit" class="nx-input nx-input-sm" placeholder="UN" maxlength="6">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">Quantidade</label>
                                    <input type="number" step="0.0001" wire:model.live="invoiceItems.{{ $i }}.quantity" class="nx-input nx-input-sm" placeholder="0.0000">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">Preço Unitário</label>
                                    <input type="number" step="0.0001" wire:model.live="invoiceItems.{{ $i }}.unit_price" class="nx-input nx-input-sm" placeholder="0.0000">
                                </div>
                                <div class="nx-form-group">
                                    <label class="nx-label" style="font-size:11px;">Total</label>
                                    <input type="number" step="0.01" wire:model="invoiceItems.{{ $i }}.total_price" class="nx-input nx-input-sm" style="background:#F1F5F9;" readonly>
                                </div>
                            </div>
                            {{-- Produto cadastrado --}}
                            <div style="margin-top:8px;">
                                <label class="nx-label" style="font-size:11px;">Vincular a Produto Cadastrado</label>
                                <select wire:model.live="invoiceItems.{{ $i }}.product_id" class="nx-input nx-input-sm">
                                    <option value="">— Selecionar produto —</option>
                                    @foreach(\App\Models\Product::orderBy('name')->limit(100)->get() as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} {{ $p->sku ? '('.$p->sku.')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:32px;color:#94A3B8;border:2px dashed #E2E8F0;border-radius:10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                            <p style="font-size:13px;margin:0;">Nenhum item. Clique em "Adicionar Item" ou importe um XML.</p>
                        </div>
                    @endforelse
                </div>

                {{-- ── TAB: TOTAIS ── --}}
                <div style="{{ $activeTab !== 'totais' ? 'display:none' : '' }}">
                    <div class="nx-form-grid">
                        <div class="nx-form-group">
                            <label class="nx-label">Valor dos Produtos</label>
                            <input type="number" step="0.01" wire:model.live="products_total" class="nx-input" placeholder="0.00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">(+) Frete</label>
                            <input type="number" step="0.01" wire:model.live="shipping_total" class="nx-input" placeholder="0.00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">(+) Seguro</label>
                            <input type="number" step="0.01" wire:model.live="insurance_total" class="nx-input" placeholder="0.00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">(+) Outras Despesas</label>
                            <input type="number" step="0.01" wire:model.live="other_expenses" class="nx-input" placeholder="0.00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">(-) Desconto</label>
                            <input type="number" step="0.01" wire:model.live="discount_total" class="nx-input" placeholder="0.00">
                        </div>
                        <div class="nx-form-group">
                            <label class="nx-label">Total de Impostos</label>
                            <input type="number" step="0.01" wire:model.live="tax_total" class="nx-input" placeholder="0.00">
                        </div>
                    </div>
                    <div class="nx-so-totals-box" style="margin-top:16px;">
                        <div class="nx-so-totals-row"><span>Produtos</span><span>R$ {{ number_format((float)$products_total, 2, ',', '.') }}</span></div>
                        <div class="nx-so-totals-row"><span>(+) Frete</span><span>R$ {{ number_format((float)$shipping_total, 2, ',', '.') }}</span></div>
                        <div class="nx-so-totals-row"><span>(+) Seguro</span><span>R$ {{ number_format((float)$insurance_total, 2, ',', '.') }}</span></div>
                        <div class="nx-so-totals-row"><span>(+) Outras Despesas</span><span>R$ {{ number_format((float)$other_expenses, 2, ',', '.') }}</span></div>
                        <div class="nx-so-totals-row"><span>(-) Desconto</span><span style="color:#EF4444">R$ {{ number_format((float)$discount_total, 2, ',', '.') }}</span></div>
                        <div class="nx-so-totals-divider"></div>
                        <div class="nx-so-totals-final">
                            <span>Valor Total</span>
                            <span>R$ {{ number_format((float)$total_value, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="nx-form-group" style="margin-top:16px;">
                        <label class="nx-label">Valor Total da Nota</label>
                        <input type="number" step="0.01" wire:model="total_value" class="nx-input" style="font-weight:700;font-size:16px;color:#059669;">
                    </div>
                </div>

                {{-- ── TAB: OBS ── --}}
                <div style="{{ $activeTab !== 'obs' ? 'display:none' : '' }}">
                    <div class="nx-form-group">
                        <label class="nx-label">Observações Internas</label>
                        <textarea wire:model="notes" class="nx-input" rows="5" placeholder="Observações sobre esta NF de Entrada..."></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="nx-so-modal-footer">
                    <button type="button" wire:click="closeModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="submit" class="nx-btn nx-btn-primary" wire:loading.attr="disabled">
                        <div wire:loading wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        </div>
                        {{ $editingId ? 'Atualizar NF' : 'Salvar NF' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MODAL — DETAIL
    ══════════════════════════════════════════════════════════ --}}
    @if($showDetail && $viewingId)
        @php $viewInv = \App\Models\FiscalInvoiceIn::with(['items.product','supplier','purchaseOrder','accountPayable'])->find($viewingId); @endphp
        @if($viewInv)
        <div class="nx-so-modal-wrap" wire:click.self="closeDetail">
            <div class="nx-so-modal">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                    <div>
                        <h2 style="font-size:17px;font-weight:700;color:#0F172A;margin:0;">
                            NF Entrada #{{ $viewInv->invoice_number }}/{{ $viewInv->series }}
                        </h2>
                        <p style="font-size:12px;color:#94A3B8;margin:2px 0 0;">
                            {{ $viewInv->supplier?->social_name ?? $viewInv->supplier_name ?? '—' }}
                            @if($viewInv->supplier_cnpj) &nbsp;·&nbsp; {{ $viewInv->supplier_cnpj }} @endif
                        </p>
                    </div>
                    <button type="button" wire:click="closeDetail"
                        style="width:32px;height:32px;border-radius:8px;border:none;background:#F1F5F9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748B;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="nx-so-modal-body">
                    {{-- Status badge --}}
                    <div style="display:flex;gap:8px;margin-bottom:16px;">
                        <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;background:{{ $viewInv->status_color }}18;color:{{ $viewInv->status_color }};border:1px solid {{ $viewInv->status_color }}33;">
                            {{ $viewInv->status_label }}
                        </span>
                        <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;background:#F1F5F9;color:#475569;text-transform:uppercase;">
                            {{ $viewInv->doc_type }}
                        </span>
                    </div>

                    {{-- Details grid --}}
                    <div class="nx-so-detail-grid" style="margin-bottom:16px;">
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">Data Emissão</span>
                            <strong>{{ $viewInv->issue_date?->format('d/m/Y') ?? '—' }}</strong>
                        </div>
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">Data Entrada</span>
                            <strong>{{ $viewInv->entry_date?->format('d/m/Y') ?? '—' }}</strong>
                        </div>
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">CFOP</span>
                            <strong>{{ $viewInv->cfop ?? '—' }}</strong>
                        </div>
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">Natureza Operação</span>
                            <strong>{{ $viewInv->operation_nature ?? '—' }}</strong>
                        </div>
                        @if($viewInv->purchaseOrder)
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">Pedido de Compra</span>
                            <strong style="color:#6366F1;">{{ $viewInv->purchaseOrder->order_number }}</strong>
                        </div>
                        @endif
                        @if($viewInv->accountPayable)
                        <div class="nx-so-detail-item">
                            <span class="nx-so-detail-label">Conta a Pagar</span>
                            <strong style="color:#10B981;">Gerada (R$ {{ number_format($viewInv->accountPayable->amount, 2, ',', '.') }})</strong>
                        </div>
                        @endif
                    </div>

                    @if($viewInv->access_key)
                    <div style="background:#F8FAFC;border-radius:8px;padding:10px 14px;margin-bottom:14px;">
                        <span style="font-size:11px;color:#64748B;display:block;margin-bottom:4px;">Chave de Acesso</span>
                        <code style="font-size:12px;color:#1E293B;word-break:break-all;">{{ $viewInv->access_key }}</code>
                    </div>
                    @endif

                    {{-- Items --}}
                    <h4 style="font-size:13px;font-weight:700;color:#0F172A;margin-bottom:10px;">Itens ({{ $viewInv->items->count() }})</h4>
                    <div class="nx-table-wrap" style="margin-bottom:16px;">
                        <table class="nx-table" style="font-size:12px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th>NCM</th>
                                    <th>CFOP</th>
                                    <th>Unid.</th>
                                    <th class="nx-th-right">Qtd</th>
                                    <th class="nx-th-right">P. Unit.</th>
                                    <th class="nx-th-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewInv->items as $k => $item)
                                <tr>
                                    <td style="color:#94A3B8;">{{ $k+1 }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $item->product_name }}</div>
                                        @if($item->product_code)
                                            <div style="font-size:11px;color:#94A3B8;font-family:monospace;">{{ $item->product_code }}</div>
                                        @endif
                                    </td>
                                    <td style="font-family:monospace;color:#64748B;">{{ $item->ncm ?? '—' }}</td>
                                    <td style="color:#64748B;">{{ $item->cfop ?? '—' }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td class="nx-td-right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                                    <td class="nx-td-right">R$ {{ number_format($item->unit_price, 4, ',', '.') }}</td>
                                    <td class="nx-td-right" style="font-weight:700;">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="nx-so-totals-box">
                        <div class="nx-so-totals-row"><span>Produtos</span><span>R$ {{ number_format($viewInv->products_total, 2, ',', '.') }}</span></div>
                        @if($viewInv->shipping_total > 0)
                            <div class="nx-so-totals-row"><span>(+) Frete</span><span>R$ {{ number_format($viewInv->shipping_total, 2, ',', '.') }}</span></div>
                        @endif
                        @if($viewInv->insurance_total > 0)
                            <div class="nx-so-totals-row"><span>(+) Seguro</span><span>R$ {{ number_format($viewInv->insurance_total, 2, ',', '.') }}</span></div>
                        @endif
                        @if($viewInv->other_expenses > 0)
                            <div class="nx-so-totals-row"><span>(+) Outras Despesas</span><span>R$ {{ number_format($viewInv->other_expenses, 2, ',', '.') }}</span></div>
                        @endif
                        @if($viewInv->discount_total > 0)
                            <div class="nx-so-totals-row"><span>(-) Desconto</span><span style="color:#EF4444">R$ {{ number_format($viewInv->discount_total, 2, ',', '.') }}</span></div>
                        @endif
                        <div class="nx-so-totals-divider"></div>
                        <div class="nx-so-totals-final">
                            <span>Valor Total</span>
                            <span>R$ {{ number_format($viewInv->total_value, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($viewInv->notes)
                        <div style="margin-top:14px;padding:12px 14px;background:#F8FAFC;border-radius:8px;border-left:3px solid #CBD5E1;">
                            <strong style="font-size:12px;color:#64748B;">Observações:</strong>
                            <p style="font-size:13px;color:#475569;margin:4px 0 0;">{{ $viewInv->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="nx-so-modal-footer">
                    <button type="button" wire:click="closeDetail" class="nx-btn nx-btn-ghost">Fechar</button>
                    <div style="display:flex;gap:8px;">
                        @if($viewInv->isEscrituravel())
                            <button type="button"
                                wire:click="escriturar({{ $viewInv->id }})"
                                wire:confirm="Escriturar esta NF? Isso irá dar entrada no estoque e gerar conta a pagar."
                                class="nx-btn" style="background:#10B981;color:#fff;border-color:#10B981;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Escriturar
                            </button>
                        @endif
                        @if(!in_array($viewInv->status, ['escriturada', 'cancelada']))
                            <button type="button" wire:click="openEdit({{ $viewInv->id }})" class="nx-btn nx-btn-outline">
                                Editar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

</div>

