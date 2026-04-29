<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Clientes</h1>
            <p class="nx-page-subtitle">Gerencie os clientes cadastrados no sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('clients.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Cliente
            </a>
            <a href="{{ route('clients.print', ['search' => $search]) }}" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar
            </a>
        </div>
    </div>

    <div class="nx-card" style="padding:16px;">
        <div class="nx-field" style="max-width:400px;margin-bottom:16px;">
            <label>Buscar cliente</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome, razão social, e-mail, CNPJ/CPF ou cidade">
        </div>

        @session('success')
            <div class="alert-success" style="position:relative;top:auto;right:auto;margin-bottom:16px;">{{ $value }}</div>
        @endsession

        @if($this->clients->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum cliente encontrado',
                'description' => 'Cadastre o primeiro cliente para começar a gerenciar sua carteira.',
                'actionLabel' => 'Novo Cliente',
                'actionRoute' => route('clients.create'),
            ])
        @else
            <div class="nx-table-wrap">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>CNPJ / CPF</th>
                            <th>Localização</th>
                            <th>Contato</th>
                            <th class="nx-th-actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->clients as $client)
                        <tr wire:key="client-{{ $client->id }}">
                            <td>
                                <div style="font-weight:600;color:#0F172A;">{{ $client->name }}</div>
                                @if($client->social_name)
                                    <div style="font-size:11.5px;color:#94A3B8;margin-top:2px;">{{ $client->social_name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="nx-badge {{ ($client->tipo_pessoa?->value ?? 'PJ') === 'PJ' ? 'nx-badge--blue' : 'nx-badge--purple' }}">
                                    {{ $client->tipo_pessoa?->label() ?? 'PJ' }}
                                </span>
                            </td>
                            <td><span class="nx-mono">{{ $client->taxNumber }}</span></td>
                            <td>
                                @if($client->address_city)
                                    <span>{{ $client->address_city }}</span>
                                    @if($client->address_state)
                                        <span style="color:#94A3B8;"> / {{ $client->address_state }}</span>
                                    @endif
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13px;">{{ $client->email }}</div>
                                @if($client->phone_number)
                                    <div style="font-size:11.5px;color:#94A3B8;margin-top:2px;">{{ $client->phone_number }}</div>
                                @endif
                            </td>
                            <td class="nx-td-actions">
                                <a href="{{ route('clients.edit', $client) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <button type="button" class="nx-action-btn nx-action-delete" title="Excluir"
                                    wire:click="deleteClient('{{ $client->id }}')"
                                    wire:confirm="Deseja excluir o cliente {{ $client->name }}?">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="nx-table-footer">
                <span class="nx-table-count">{{ $this->clients->count() }} {{ $this->clients->count() === 1 ? 'cliente' : 'clientes' }}</span>
            </div>
        @endif
    </div>

</div>

