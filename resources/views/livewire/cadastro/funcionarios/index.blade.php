<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Funcionarios</h1>
            <p class="nx-page-subtitle">Gerencie os funcionarios cadastrados no sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('employees.create') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Funcionario
            </a>
            <a href="{{ route('employees.print', ['search' => $search]) }}" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar
            </a>
        </div>
    </div>

    <div class="nx-card" style="padding:16px;">
        <div class="nx-field" style="max-width:360px;margin-bottom:16px;">
            <label>Buscar funcionario</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome, funcao, email ou CPF">
        </div>

        @if($this->employees->isEmpty())
            @include('partials.empty-state', [
                'title' => 'Nenhum funcionario cadastrado',
                'description' => 'Adicione o primeiro funcionario para gerenciar sua equipe.',
                'actionLabel' => 'Novo Funcionario',
                'actionRoute' => route('employees.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Funcao</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Endereco</th>
                        <th>CPF</th>
                        <th class="nx-th-actions">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->employees as $employee)
                    <tr wire:key="employee-{{ $employee->id }}">
                        <td><strong>{{ $employee->name }}</strong></td>
                        <td>{{ $employee->role ?: '-' }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone_number ?: '-' }}</td>
                        <td>{{ $employee->address ?: '-' }}</td>
                        <td><span style="font-family:monospace;font-size:12.5px;">{{ $employee->identification_number }}</span></td>
                        <td class="nx-td-actions">
                            <a href="{{ route('employees.edit', $employee) }}" class="nx-action-btn nx-action-edit" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="deleteEmployee('{{ $employee->id }}')"
                                wire:confirm="Deseja excluir o funcionario {{ $employee->name }}?"
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
            <span class="nx-table-count">{{ $this->employees->count() }} {{ $this->employees->count() === 1 ? 'funcionario' : 'funcionarios' }}</span>
        </div>
        @endif
    </div>

</div>

