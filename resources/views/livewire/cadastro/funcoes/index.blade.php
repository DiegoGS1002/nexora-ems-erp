<div class="nx-list-page">

    {{-- ═══════════════════════════════════════
         HEADER
         ═══════════════════════════════════════ --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Funções / Cargos</h1>
            <p class="nx-page-subtitle">Gerencie as funções e permissões de acesso ao sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('roles.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Função
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

    <div class="nx-card" style="padding:16px;">
        <div class="nx-field" style="max-width:360px;margin-bottom:16px;">
            <label>Buscar função</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome, departamento ou código...">
        </div>

        @if($this->roles->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhuma função cadastrada',
                'description' => 'Adicione a primeira função para definir as permissões de acesso.',
                'actionLabel' => 'Nova Função',
                'actionRoute' => route('roles.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Departamento</th>
                        <th>Código</th>
                        <th>Função Superior</th>
                        <th>Status</th>
                        <th>Atribuição</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->roles as $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if($role->description)
                                <br><small style="color:#94A3B8;font-size:11.5px;">{{ str($role->description)->limit(60) }}</small>
                            @endif
                        </td>
                        <td>{{ $role->department ?: '-' }}</td>
                        <td>
                            @if($role->code)
                                <span style="font-family:monospace;font-size:12px;background:#F1F5F9;padding:2px 7px;border-radius:4px;color:#475569;">{{ $role->code }}</span>
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                        <td>{{ $role->parentRole?->name ?? '-' }}</td>
                        <td>
                            @if($role->is_active)
                                <span class="nx-roles-badge nx-roles-badge--active">Ativo</span>
                            @else
                                <span class="nx-roles-badge nx-roles-badge--inactive">Inativo</span>
                            @endif
                        </td>
                        <td>
                            @if($role->allow_assignment)
                                <span style="font-size:12px;color:#10B981;font-weight:600;">✓ Permitida</span>
                            @else
                                <span style="font-size:12px;color:#94A3B8;font-weight:500;">✕ Bloqueada</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('roles.edit', $role) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="Deseja excluir a função '{{ $role->name }}'? Esta ação não pode ser desfeita."
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
            <span class="nx-table-count">
                {{ $this->roles->count() }} {{ $this->roles->count() === 1 ? 'função' : 'funções' }}
            </span>
        </div>
        @endif
    </div>

</div>

