<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Unidades de Medida</h1>
            <p class="nx-page-subtitle">Gerencie as unidades de medida utilizadas no cadastro de produtos</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('products.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Produtos
            </a>
            <a href="{{ route('units-of-measure.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Unidade
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
        <div class="nx-field" style="max-width:360px;margin-bottom:16px;">
            <label>Buscar unidade</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome ou sigla (ex: KG, UN)...">
        </div>

        @if($units->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhuma unidade cadastrada',
                'description' => 'Adicione unidades de medida para utilizar no cadastro de produtos.',
                'actionLabel' => 'Nova Unidade',
                'actionRoute' => route('units-of-measure.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Sigla</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th class="nx-th-center">Produtos</th>
                        <th class="nx-th-center">Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $unit)
                    <tr wire:key="unit-{{ $unit->id }}">
                        <td>
                            <span style="font-family:monospace;font-size:13px;font-weight:700;background:#EEF2FF;color:#4F46E5;padding:3px 10px;border-radius:6px;">{{ $unit->abbreviation }}</span>
                        </td>
                        <td><strong>{{ $unit->name }}</strong></td>
                        <td>
                            @if($unit->description)
                                <span title="{{ $unit->description }}" style="display:block;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#64748B;font-size:13px;">{{ $unit->description }}</span>
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            <span class="nx-badge {{ $unit->products_count > 0 ? 'nx-badge-success' : 'nx-badge-gray' }}">
                                {{ $unit->products_count }}
                            </span>
                        </td>
                        <td class="nx-td-center">
                            @if($unit->is_active)
                                <span class="nx-roles-badge nx-roles-badge--active">Ativa</span>
                            @else
                                <span class="nx-roles-badge nx-roles-badge--inactive">Inativa</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('units-of-measure.edit', $unit) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="deleteUnit('{{ $unit->id }}')"
                                wire:confirm="Deseja excluir a unidade '{{ $unit->abbreviation }} — {{ $unit->name }}'? Esta ação não pode ser desfeita."
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
            <span class="nx-table-count">{{ $units->total() }} {{ $units->total() === 1 ? 'unidade' : 'unidades' }}</span>
            <div class="nx-pagination">{{ $units->links() }}</div>
        </div>
        @endif
    </div>

</div>

