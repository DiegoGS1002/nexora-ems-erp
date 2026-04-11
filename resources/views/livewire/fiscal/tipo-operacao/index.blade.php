<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Tipos de Operação Fiscal</h1>
            <p class="nx-page-subtitle">Gerencie os tipos de operação com CFOP, ICMS, IPI, PIS e COFINS</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('fiscal.nfe.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Notas Fiscais
            </a>
            <a href="{{ route('fiscal.tipo-operacao.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Tipo de Operação
            </a>
        </div>
    </div>

    {{-- FLASH --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    @session('error')
        <div class="alert-error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $value }}
        </div>
    @endsession

    <div class="nx-card" style="padding:16px;">
        {{-- Filtros --}}
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;align-items:flex-end;">
            <div class="nx-field" style="flex:1;min-width:220px;margin-bottom:0;">
                <label>Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Código, descrição, natureza ou CFOP...">
            </div>
            <div class="nx-field" style="min-width:160px;margin-bottom:0;">
                <label>Tipo de Movimento</label>
                <select wire:model.live="filterMovimento">
                    <option value="">Todos</option>
                    @foreach($movimentos as $mov)
                        <option value="{{ $mov->value }}">{{ $mov->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="nx-field" style="min-width:120px;margin-bottom:0;">
                <label>CFOP</label>
                <input type="text" wire:model.live.debounce.300ms="filterCfop" placeholder="Ex: 5102" maxlength="4">
            </div>
            @if($search || $filterMovimento || $filterCfop)
                <button type="button" class="nx-btn nx-btn-ghost" wire:click="$set('search','');$set('filterMovimento','');$set('filterCfop','')" style="align-self:flex-end;">
                    Limpar filtros
                </button>
            @endif
        </div>

        @if($operacoes->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum tipo de operação cadastrado',
                'description' => 'Cadastre os tipos de operação fiscal com CFOP, ICMS, IPI, PIS e COFINS.',
                'actionLabel' => 'Novo Tipo de Operação',
                'actionRoute' => route('fiscal.tipo-operacao.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição / Natureza</th>
                        <th class="nx-th-center">CFOP</th>
                        <th class="nx-th-center">Movimento</th>
                        <th class="nx-th-center">ICMS</th>
                        <th class="nx-th-center">IPI</th>
                        <th class="nx-th-center">PIS</th>
                        <th class="nx-th-center">COFINS</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operacoes as $op)
                    <tr wire:key="op-{{ $op->id }}">
                        <td>
                            <span style="font-family:monospace;font-size:12px;background:#F1F5F9;padding:2px 7px;border-radius:4px;color:#1E3A5F;font-weight:600;">{{ $op->codigo }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#1E293B;font-size:13px;">{{ $op->descricao }}</div>
                            @if($op->natureza_operacao)
                                <div style="font-size:11px;color:#64748B;margin-top:2px;">{{ $op->natureza_operacao }}</div>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            @if($op->cfop)
                                <span style="font-family:monospace;font-size:13px;font-weight:700;color:#1D4ED8;">{{ $op->cfop }}</span>
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            @php $mov = $op->tipo_movimento instanceof \App\Enums\TipoMovimentoFiscal ? $op->tipo_movimento : \App\Enums\TipoMovimentoFiscal::from($op->tipo_movimento) @endphp
                            <span class="nx-badge {{ $mov->badgeClass() }}">{{ $mov->label() }}</span>
                        </td>
                        {{-- ICMS CST indicator --}}
                        <td class="nx-td-center">
                            @if($op->icms_cst)
                                <span title="CST ICMS: {{ $op->icms_cst }} | Alíq: {{ $op->icms_aliquota ?? '—' }}%" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#10B981;display:inline-block;"></span>
                                    {{ $op->icms_cst }}
                                </span>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        {{-- IPI CST indicator --}}
                        <td class="nx-td-center">
                            @if($op->ipi_cst)
                                <span title="CST IPI: {{ $op->ipi_cst }} | Alíq: {{ $op->ipi_aliquota ?? '—' }}%" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#F59E0B;display:inline-block;"></span>
                                    {{ $op->ipi_cst }}
                                </span>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        {{-- PIS CST indicator --}}
                        <td class="nx-td-center">
                            @if($op->pis_cst)
                                <span title="CST PIS: {{ $op->pis_cst }} | Alíq: {{ $op->pis_aliquota ?? '—' }}%" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#6366F1;display:inline-block;"></span>
                                    {{ $op->pis_cst }}
                                </span>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        {{-- COFINS CST indicator --}}
                        <td class="nx-td-center">
                            @if($op->cofins_cst)
                                <span title="CST COFINS: {{ $op->cofins_cst }} | Alíq: {{ $op->cofins_aliquota ?? '—' }}%" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#EC4899;display:inline-block;"></span>
                                    {{ $op->cofins_cst }}
                                </span>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            @if($op->is_active)
                                <span class="nx-roles-badge nx-roles-badge--active">Ativo</span>
                            @else
                                <span class="nx-roles-badge nx-roles-badge--inactive">Inativo</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('fiscal.tipo-operacao.edit', $op) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="delete({{ $op->id }})"
                                wire:confirm="Deseja excluir o tipo '{{ $op->codigo }} – {{ $op->descricao }}'? Esta ação não pode ser desfeita."
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="nx-table-footer">
            <span class="nx-table-count">{{ $operacoes->total() }} {{ $operacoes->total() === 1 ? 'tipo' : 'tipos' }} de operação</span>
            <div class="nx-pagination">{{ $operacoes->links() }}</div>
        </div>
        @endif
    </div>

</div>

