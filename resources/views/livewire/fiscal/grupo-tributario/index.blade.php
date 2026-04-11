<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Grupos Tributários</h1>
            <p class="nx-page-subtitle">Configure grupos com NCM, ICMS, IPI, PIS e COFINS para vincular aos produtos</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('fiscal.tipo-operacao.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Tipos de Operação
            </a>
            <a href="{{ route('fiscal.grupo-tributario.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Grupo
            </a>
        </div>
    </div>

    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="alert-error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $value }}
        </div>
    @endsession

    <div class="nx-card" style="padding:16px;">
        {{-- Filtros --}}
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;align-items:flex-end;">
            <div class="nx-field" style="flex:1;min-width:220px;margin-bottom:0;">
                <label>Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Código, nome ou NCM...">
            </div>
            <div class="nx-field" style="min-width:180px;margin-bottom:0;">
                <label>Regime Tributário</label>
                <select wire:model.live="filterRegime">
                    <option value="">Todos</option>
                    @foreach($regimes as $r)
                        <option value="{{ $r->value }}">{{ $r->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if($search || $filterRegime)
                <button type="button" class="nx-btn nx-btn-ghost" wire:click="$set('search','');$set('filterRegime','')" style="align-self:flex-end;">Limpar</button>
            @endif
        </div>

        @if($grupos->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum grupo tributário cadastrado',
                'description' => 'Crie grupos para organizar as tributações dos seus produtos.',
                'actionLabel' => 'Novo Grupo',
                'actionRoute' => route('fiscal.grupo-tributario.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Código / Nome</th>
                        <th class="nx-th-center">Regime</th>
                        <th class="nx-th-center">NCM</th>
                        <th>Op. Saída</th>
                        <th>Op. Entrada</th>
                        <th class="nx-th-center">ICMS CST</th>
                        <th class="nx-th-center">PIS/COF.</th>
                        <th class="nx-th-center">Produtos</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $grupo)
                    <tr wire:key="grupo-{{ $grupo->id }}">
                        <td>
                            <div style="font-family:monospace;font-size:11px;background:#F1F5F9;padding:2px 7px;border-radius:4px;color:#1E3A5F;font-weight:700;display:inline-block;margin-bottom:3px;">{{ $grupo->codigo }}</div>
                            <div style="font-weight:600;font-size:13px;color:#1E293B;">{{ $grupo->nome }}</div>
                        </td>
                        <td class="nx-td-center">
                            @php $r = $grupo->regime_tributario instanceof \App\Enums\RegimeTributario ? $grupo->regime_tributario : \App\Enums\RegimeTributario::from($grupo->regime_tributario) @endphp
                            <span class="nx-badge {{ $r->badgeClass() }}" style="font-size:10px;">{{ $r->label() }}</span>
                        </td>
                        <td class="nx-td-center">
                            @if($grupo->ncm)
                                <span style="font-family:monospace;font-size:12px;font-weight:600;color:#374151;">{{ $grupo->ncm_formatado }}</span>
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($grupo->tipoOperacaoSaida)
                                <div style="font-size:11px;font-family:monospace;font-weight:700;color:#1D4ED8;">{{ $grupo->tipoOperacaoSaida->cfop }}</div>
                                <div style="font-size:11px;color:#64748B;">{{ Str::limit($grupo->tipoOperacaoSaida->descricao, 22) }}</div>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($grupo->tipoOperacaoEntrada)
                                <div style="font-size:11px;font-family:monospace;font-weight:700;color:#6366F1;">{{ $grupo->tipoOperacaoEntrada->cfop }}</div>
                                <div style="font-size:11px;color:#64748B;">{{ Str::limit($grupo->tipoOperacaoEntrada->descricao, 22) }}</div>
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            @if($grupo->icms_cst)
                                <span title="Alíq: {{ $grupo->icms_aliquota ?? '—' }}%" style="font-family:monospace;font-size:13px;font-weight:700;color:#10B981;">{{ $grupo->icms_cst }}</span>
                                @if($grupo->icms_aliquota !== null)
                                    <div style="font-size:10px;color:#6B7280;">{{ $grupo->icms_aliquota }}%</div>
                                @endif
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            <div style="display:flex;flex-direction:column;gap:2px;align-items:center;">
                                @if($grupo->pis_cst)
                                    <span style="font-size:10px;color:#6366F1;font-weight:600;">PIS {{ $grupo->pis_cst }} @if($grupo->pis_aliquota)({{ $grupo->pis_aliquota }}%)@endif</span>
                                @endif
                                @if($grupo->cofins_cst)
                                    <span style="font-size:10px;color:#EC4899;font-weight:600;">COF {{ $grupo->cofins_cst }} @if($grupo->cofins_aliquota)({{ $grupo->cofins_aliquota }}%)@endif</span>
                                @endif
                                @if(!$grupo->pis_cst && !$grupo->cofins_cst)
                                    <span style="color:#CBD5E1;font-size:12px;">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="nx-td-center">
                            <span class="nx-badge {{ $grupo->products_count > 0 ? 'nx-badge-success' : 'nx-badge-gray' }}">{{ $grupo->products_count }}</span>
                        </td>
                        <td class="nx-td-center">
                            @if($grupo->is_active)
                                <span class="nx-roles-badge nx-roles-badge--active">Ativo</span>
                            @else
                                <span class="nx-roles-badge nx-roles-badge--inactive">Inativo</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('fiscal.grupo-tributario.edit', $grupo) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button type="button" class="nx-action-btn nx-action-delete" title="Excluir"
                                wire:click="delete({{ $grupo->id }})"
                                wire:confirm="Excluir '{{ $grupo->codigo }} – {{ $grupo->nome }}'? Produtos vinculados perderão a referência.">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="nx-table-footer">
            <span class="nx-table-count">{{ $grupos->total() }} {{ $grupos->total() === 1 ? 'grupo' : 'grupos' }}</span>
            <div class="nx-pagination">{{ $grupos->links() }}</div>
        </div>
        @endif
    </div>

</div>

