<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Veículos</h1>
            <p class="nx-page-subtitle">Gerencie a frota de veículos da empresa</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('vehicles.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Veículo
            </a>
            <a href="{{ route('vehicles.print') }}" class="nx-btn nx-btn-outline" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar
            </a>
        </div>
    </div>

    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
            style="max-width:1200px;margin:0 auto 16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    <div class="nx-card" style="padding:16px;">
        <div class="nx-field" style="max-width:360px;margin-bottom:16px;">
            <label>Buscar veículo</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Placa, marca, modelo, motorista...">
        </div>

        @if($this->vehicles->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum veículo cadastrado',
                'description' => 'Adicione o primeiro veículo para gerenciar sua frota.',
                'actionLabel' => 'Novo Veículo',
                'actionRoute' => route('vehicles.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Marca / Modelo</th>
                        <th>Ano</th>
                        <th>Tipo</th>
                        <th>Combustível</th>
                        <th>Motorista</th>
                        <th>Status</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->vehicles as $vehicle)
                    <tr wire:key="vehicle-{{ $vehicle->id }}">
                        <td>
                            <span style="font-family:monospace;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;background:#F1F5F9;padding:3px 8px;border-radius:6px;color:#334155;">
                                {{ $vehicle->plate }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $vehicle->brand }}</strong>
                            <span style="color:#64748B;"> {{ $vehicle->model }}</span>
                        </td>
                        <td>{{ $vehicle->manufacturing_year ?? $vehicle->year ?? '—' }}</td>
                        <td>{{ $vehicle->vehicle_type?->label() ?? '—' }}</td>
                        <td>{{ $vehicle->fuel_type?->label() ?? '—' }}</td>
                        <td>{{ $vehicle->responsible_driver ?? '—' }}</td>
                        <td>
                            @if($vehicle->is_active)
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#059669;background:#ECFDF5;padding:3px 10px;border-radius:20px;">
                                    <span style="width:6px;height:6px;background:#10B981;border-radius:50%;display:inline-block;"></span>
                                    Ativo
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#DC2626;background:#FEF2F2;padding:3px 10px;border-radius:20px;">
                                    <span style="width:6px;height:6px;background:#EF4444;border-radius:50%;display:inline-block;"></span>
                                    Inativo
                                </span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="nx-action-btn nx-action-edit" title="Editar" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="deleteVehicle({{ $vehicle->id }})"
                                wire:confirm="Deseja excluir o veículo {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->plate }})?"
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
            <span class="nx-table-count">{{ $this->vehicles->count() }} {{ $this->vehicles->count() === 1 ? 'veículo' : 'veículos' }}</span>
        </div>
        @endif
    </div>

</div>

