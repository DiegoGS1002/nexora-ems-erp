<div class="nx-product-page">

    {{-- ═══════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════ --}}
    <div class="nx-form-header" style="max-width:1200px;margin:0 auto 24px;">
        <a href="{{ route('vehicles.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Veículos
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Veículo' : 'Novo Veículo' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados do veículo da frota' : 'Preencha as informações para cadastrar um novo veículo na frota' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($isEditing)
                    <span class="nx-status-badge nx-status-badge--edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editando
                    </span>
                @endif
                <a href="{{ route('vehicles.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
                <button form="vehicle-form" type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-primary">
                    <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Salvar Veículo' }}
                </button>
            </div>
        </div>
    </div>

    {{-- FLASH / ERRORS --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
            style="max-width:1200px;margin:0 auto 16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    @if ($errors->any())
        <div class="alert-error" style="max-width:1200px;margin:0 auto 16px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="vehicle-form" wire:submit="save" enctype="multipart/form-data" style="max-width:1200px;margin:0 auto;">

        {{-- ═══════ TABS ═══════ --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key' => 'dados-gerais',  'label' => 'Dados Gerais',  'icon' => '<rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
                    ['key' => 'documentos',    'label' => 'Documentos',    'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                    ['key' => 'manutencao',    'label' => 'Manutenção',    'icon' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'],
                    ['key' => 'seguro',        'label' => 'Seguro',        'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
                    ['key' => 'custos',        'label' => 'Custos',        'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                    ['key' => 'observacoes',   'label' => 'Observações',   'icon' => '<line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/>'],
                    ['key' => 'historico',     'label' => 'Histórico',     'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button type="button"
                    wire:click="$set('activeTab', '{{ $tab['key'] }}')"
                    class="nx-product-tab {{ $activeTab === $tab['key'] ? 'nx-product-tab--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $tab['icon'] !!}</svg>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- ═══════ LAYOUT (main + sidebar) ═══════ --}}
        <div class="nx-product-layout">

            {{-- ─── COLUNA PRINCIPAL ─── --}}
            <div class="nx-product-main">

                {{-- ══════════════════════════════════════════════
                     ABA: DADOS GERAIS
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'dados-gerais'])>

                    {{-- IDENTIFICAÇÃO DO VEÍCULO --}}
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Identificação do Veículo</h3>
                                <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                            </div>

                            {{-- Placa / RENAVAM / Chassi --}}
                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Placa <span class="nx-required">*</span></label>
                                    <div style="position:relative;">
                                        <input type="text" wire:model.blur="form.plate"
                                            placeholder="ABC1D23 ou ABC-1234"
                                            maxlength="8"
                                            style="text-transform:uppercase;font-family:monospace;font-weight:700;letter-spacing:0.08em;font-size:14px;">
                                        @if($form->plate && !$errors->has('form.plate'))
                                            <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#10B981;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    @error('form.plate') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>RENAVAM <span class="nx-required">*</span></label>
                                    <div style="position:relative;">
                                        <input type="text" wire:model.blur="form.renavam"
                                            placeholder="00000000000"
                                            maxlength="11"
                                            style="font-family:monospace;letter-spacing:0.05em;">
                                        @if($form->renavam && !$errors->has('form.renavam'))
                                            <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#10B981;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    @error('form.renavam') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Chassi <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.chassis"
                                        placeholder="17 caracteres alfanuméricos"
                                        maxlength="17"
                                        style="font-family:monospace;font-size:12px;letter-spacing:0.04em;text-transform:uppercase;">
                                    @error('form.chassis') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Tipo / Categoria / Espécie --}}
                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Tipo de Veículo <span class="nx-required">*</span></label>
                                    <select wire:model="form.vehicle_type">
                                        <option value="">Selecione</option>
                                        @foreach($tiposVeiculo as $tipo)
                                            <option value="{{ $tipo->value }}" @selected($form->vehicle_type === $tipo->value)>{{ $tipo->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.vehicle_type') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Categoria <span class="nx-required">*</span></label>
                                    <select wire:model="form.category">
                                        <option value="">Selecione</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->value }}" @selected($form->category === $cat->value)>{{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.category') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Espécie <span class="nx-required">*</span></label>
                                    <select wire:model="form.species">
                                        <option value="">Selecione</option>
                                        @foreach($especies as $esp)
                                            <option value="{{ $esp->value }}" @selected($form->species === $esp->value)>{{ $esp->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.species') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Anos --}}
                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Ano Fabricação <span class="nx-required">*</span></label>
                                    <select wire:model="form.manufacturing_year">
                                        <option value="">Selecione</option>
                                        @for($y = date('Y') + 1; $y >= 1960; $y--)
                                            <option value="{{ $y }}" @selected($form->manufacturing_year == $y)>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('form.manufacturing_year') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Ano Modelo <span class="nx-required">*</span></label>
                                    <select wire:model="form.model_year">
                                        <option value="">Selecione</option>
                                        @for($y = date('Y') + 2; $y >= 1960; $y--)
                                            <option value="{{ $y }}" @selected($form->model_year == $y)>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('form.model_year') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Marca / Modelo --}}
                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Marca <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.brand" placeholder="Ex: Toyota, Volkswagen, Mercedes..." maxlength="100">
                                    @error('form.brand') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Modelo <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.model" placeholder="Ex: Corolla, Sprinter, HB20..." maxlength="100">
                                    @error('form.model') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Cor / Combustível --}}
                            <div class="grid grid-2">
                                <div class="nx-field">
                                    <label>Cor <span class="nx-required">*</span></label>
                                    <select wire:model="form.color">
                                        <option value="">Selecione</option>
                                        @foreach(['Branco','Prata','Cinza','Preto','Azul','Vermelho','Verde','Amarelo','Laranja','Marrom','Bege','Vinho','Dourado','Outro'] as $cor)
                                            <option value="{{ $cor }}" @selected($form->color === $cor)>{{ $cor }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.color') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Combustível <span class="nx-required">*</span></label>
                                    <select wire:model="form.fuel_type">
                                        <option value="">Selecione</option>
                                        @foreach($combustiveis as $comb)
                                            <option value="{{ $comb->value }}" @selected($form->fuel_type === $comb->value)>{{ $comb->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.fuel_type') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INFORMAÇÕES TÉCNICAS --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Informações Técnicas</h3>
                                <span class="nx-section-hint">Dados de performance e especificações</span>
                            </div>

                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Potência (cv)</label>
                                    <div style="position:relative;">
                                        <input type="text" wire:model.blur="form.power_hp" placeholder="Ex: 150" maxlength="10">
                                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:11px;color:#94A3B8;font-weight:600;">cv</span>
                                    </div>
                                </div>
                                <div class="nx-field">
                                    <label>Cilindradas (cm³)</label>
                                    <div style="position:relative;">
                                        <input type="text" wire:model.blur="form.displacement_cc" placeholder="Ex: 1600" maxlength="10">
                                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:11px;color:#94A3B8;font-weight:600;">cm³</span>
                                    </div>
                                </div>
                                <div class="nx-field">
                                    <label>Nº de Portas</label>
                                    <select wire:model="form.doors">
                                        <option value="">Selecione</option>
                                        @foreach([2,3,4,5] as $d)
                                            <option value="{{ $d }}" @selected($form->doors == $d)>{{ $d }} portas</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Cap. de Passageiros</label>
                                    <input type="number" wire:model.blur="form.passenger_capacity" placeholder="Ex: 5" min="1">
                                </div>
                                <div class="nx-field">
                                    <label>Tipo de Câmbio</label>
                                    <select wire:model="form.transmission_type">
                                        <option value="">Selecione</option>
                                        <option value="manual" @selected($form->transmission_type === 'manual')>Manual</option>
                                        <option value="automatico" @selected($form->transmission_type === 'automatico')>Automático</option>
                                        <option value="automatizado" @selected($form->transmission_type === 'automatizado')>Automatizado</option>
                                        <option value="cvt" @selected($form->transmission_type === 'cvt')>CVT</option>
                                    </select>
                                </div>
                                <div class="nx-field">
                                    <label>Tração</label>
                                    <select wire:model="form.traction_type">
                                        <option value="">Selecione</option>
                                        <option value="dianteira" @selected($form->traction_type === 'dianteira')>Dianteira (FWD)</option>
                                        <option value="traseira" @selected($form->traction_type === 'traseira')>Traseira (RWD)</option>
                                        <option value="4x4" @selected($form->traction_type === '4x4')>4x4 (AWD/4WD)</option>
                                        <option value="integral" @selected($form->traction_type === 'integral')>Integral</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-3">
                                <div class="nx-field">
                                    <label>Peso Bruto (kg)</label>
                                    <input type="number" step="0.01" wire:model.blur="form.gross_weight" placeholder="0,00">
                                </div>
                                <div class="nx-field">
                                    <label>Peso Líquido (kg)</label>
                                    <input type="number" step="0.01" wire:model.blur="form.net_weight" placeholder="0,00">
                                </div>
                                <div class="nx-field">
                                    <label>Cap. de Carga (kg)</label>
                                    <input type="number" step="0.01" wire:model.blur="form.cargo_capacity" placeholder="0,00">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- VINCULAÇÃO E LOCALIZAÇÃO --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Vinculação e Localização</h3>
                                <span class="nx-section-hint">Responsabilidade e paradeiro atual</span>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Departamento</label>
                                    <select wire:model="form.department">
                                        <option value="">Selecione o departamento</option>
                                        <option value="administrativo">Administrativo</option>
                                        <option value="comercial">Comercial</option>
                                        <option value="financeiro">Financeiro</option>
                                        <option value="logistica">Logística</option>
                                        <option value="producao">Produção</option>
                                        <option value="rh">Recursos Humanos</option>
                                        <option value="ti">TI</option>
                                        <option value="diretoria">Diretoria</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="nx-field">
                                    <label>Motorista Responsável</label>
                                    <input type="text" wire:model.blur="form.responsible_driver"
                                        placeholder="Nome do motorista responsável" maxlength="150">
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Centro de Custo</label>
                                    <input type="text" wire:model.blur="form.cost_center"
                                        placeholder="Ex: CC-001 — Operacional" maxlength="100">
                                </div>
                                <div class="nx-field">
                                    <label>Unidade</label>
                                    <input type="text" wire:model.blur="form.unit"
                                        placeholder="Ex: Filial São Paulo" maxlength="100">
                                </div>
                            </div>

                            <div class="grid grid-2">
                                <div class="nx-field">
                                    <label>Localização Atual</label>
                                    <input type="text" wire:model.blur="form.current_location"
                                        placeholder="Ex: Pátio Central — Galpão 2" maxlength="150">
                                </div>
                                <div class="nx-field">
                                    <label>Observação de Localização</label>
                                    <input type="text" wire:model.blur="form.location_note"
                                        placeholder="Ex: Próximo ao portão de entrada" maxlength="255">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /dados-gerais --}}

                {{-- ══════════════════════════════════════════════
                     ABA: DOCUMENTOS
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'documentos'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Documentos do Veículo</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Upload de documentos (CRLV, DUT, laudo de vistoria) estará disponível em breve.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════
                     ABA: MANUTENÇÃO
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'manutencao'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Registro de Manutenção</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Módulo de manutenção preventiva e corretiva em desenvolvimento. Em breve você poderá registrar revisões, trocas de óleo e agendamentos.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════
                     ABA: SEGURO
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'seguro'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Dados do Seguro</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Módulo de seguros (apólice, seguradora, vencimento, cobertura) em desenvolvimento.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════
                     ABA: CUSTOS
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'custos'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Controle de Custos</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Módulo de custos (abastecimento, depreciação, multas) em desenvolvimento. Integração com o módulo Financeiro em breve.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════
                     ABA: OBSERVAÇÕES
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'observacoes'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Observações Internas</h3>
                                <span class="nx-section-hint">Visível apenas para a equipe</span>
                            </div>
                            <div class="nx-field">
                                <label>Anotações e observações sobre o veículo</label>
                                <textarea wire:model.blur="form.observations" rows="8"
                                    placeholder="Registre aqui informações adicionais, histórico, características especiais ou quaisquer anotações relevantes sobre este veículo..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════
                     ABA: HISTÓRICO
                     ══════════════════════════════════════════════ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'historico'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Histórico do Veículo</h3>
                            </div>
                            @if($isEditing && $vehicle)
                                <div style="display:flex;flex-direction:column;gap:12px;">
                                    <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;background:#F8FAFC;border-radius:10px;border:1px solid #E2E8F0;">
                                        <div style="width:32px;height:32px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        </div>
                                        <div>
                                            <p style="font-size:13px;font-weight:600;color:#1E293B;margin:0 0 2px;">Veículo cadastrado no sistema</p>
                                            <p style="font-size:12px;color:#64748B;margin:0;">{{ $vehicle->created_at?->format('d/m/Y \à\s H:i') }}</p>
                                        </div>
                                    </div>
                                    @if($vehicle->updated_at && $vehicle->updated_at->ne($vehicle->created_at))
                                        <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;background:#F8FAFC;border-radius:10px;border:1px solid #E2E8F0;">
                                            <div style="width:32px;height:32px;background:#F0FDF4;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </div>
                                            <div>
                                                <p style="font-size:13px;font-weight:600;color:#1E293B;margin:0 0 2px;">Última atualização</p>
                                                <p style="font-size:12px;color:#64748B;margin:0;">{{ $vehicle->updated_at?->format('d/m/Y \à\s H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="nx-inline-alert nx-inline-alert--warn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span>O histórico estará disponível após o cadastro do veículo.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>{{-- /nx-product-main --}}

            {{-- ─── SIDEBAR ─── --}}
            <aside class="nx-product-sidebar">

                {{-- STATUS E AQUISIÇÃO --}}
                <div class="nx-sidebar-card">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Status e Aquisição
                    </div>
                    <div class="nx-sidebar-card-body">

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;cursor:pointer;gap:12px;margin-bottom:4px;">
                            <div>
                                <span style="font-size:13px;font-weight:600;color:#1E293B;display:block;">Status do Veículo</span>
                                <span style="font-size:11.5px;font-weight:700;color:{{ $form->is_active ? '#059669' : '#DC2626' }};">
                                    {{ $form->is_active ? '● Operacional (Ativo)' : '● Baixado / Inativo' }}
                                </span>
                            </div>
                            <span class="nx-switch">
                                <input type="checkbox" wire:model.live="form.is_active">
                                <span class="nx-switch-track"></span>
                            </span>
                        </label>

                        <div class="nx-field" style="margin-top:12px;padding-top:12px;border-top:1px solid #F1F5F9;">
                            <label style="font-size:11.5px;">Data de Aquisição</label>
                            <input type="date" wire:model.blur="form.acquisition_date" style="font-size:13px;">
                        </div>

                        <div class="nx-field" style="margin-top:10px;">
                            <label style="font-size:11.5px;">Valor de Aquisição (R$)</label>
                            <div style="position:relative;">
                                <span class="nx-input-prefix" style="font-size:11.5px;">R$</span>
                                <input type="number" step="0.01" wire:model.blur="form.acquisition_value"
                                    placeholder="0,00" style="padding-left:36px;font-size:13px;">
                            </div>
                            <small>Valor de nota fiscal para cálculo de depreciação</small>
                        </div>
                    </div>
                </div>

                {{-- GALERIA DE FOTOS --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Fotos do Veículo
                    </div>
                    <div class="nx-sidebar-card-body" style="padding:14px;">

                        {{-- Fotos existentes --}}
                        @if(!empty($existingPhotos))
                            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:10px;">
                                @foreach($existingPhotos as $idx => $photo)
                                    <div style="position:relative;">
                                        <img src="{{ asset('storage/'.$photo) }}"
                                            alt="Foto {{ $idx + 1 }}"
                                            style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;border:1px solid #E2E8F0;display:block;">
                                        <button type="button"
                                            wire:click="removeExistingPhoto({{ $idx }})"
                                            style="position:absolute;top:4px;right:4px;width:22px;height:22px;background:#FFF;border:1px solid #E2E8F0;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,0.12);color:#64748B;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Preview de novas fotos --}}
                        @if(!empty($newPhotos))
                            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:10px;">
                                @foreach($newPhotos as $idx => $photo)
                                    <div style="position:relative;">
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            alt="Nova foto {{ $idx + 1 }}"
                                            style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;border:2px solid #3B82F6;display:block;">
                                        <button type="button"
                                            wire:click="removeNewPhoto({{ $idx }})"
                                            style="position:absolute;top:4px;right:4px;width:22px;height:22px;background:#FFF;border:1px solid #E2E8F0;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,0.12);color:#64748B;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <p style="font-size:11.5px;color:#3B82F6;font-weight:600;margin-bottom:10px;text-align:center;">
                                {{ count($newPhotos) }} nova(s) foto(s) para upload
                            </p>
                        @endif

                        {{-- Dropzone de upload --}}
                        <div class="nx-dropzone" x-data="{dragover:false}"
                            @dragover.prevent="dragover=true" @dragleave="dragover=false" @drop.prevent="dragover=false"
                            :class="dragover && 'nx-dropzone--over'"
                            style="min-height:110px;">
                            <div class="nx-dropzone-placeholder" style="padding:18px 12px;">
                                <div class="nx-dropzone-icon" style="width:40px;height:40px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                                <p class="nx-dropzone-label" style="font-size:12px;text-align:center;">
                                    Arraste ou <strong>clique para adicionar</strong>
                                </p>
                                <p class="nx-dropzone-hint">PNG, JPG, WEBP — até 5MB cada</p>
                            </div>
                            <input type="file" wire:model="newPhotos" accept="image/png,image/jpeg,image/webp"
                                multiple class="nx-dropzone-input">
                        </div>

                        @if(!empty($existingPhotos) || !empty($newPhotos))
                            <button type="button"
                                wire:click="$set('existingPhotos', []); $set('newPhotos', [])"
                                class="nx-btn nx-btn-ghost"
                                style="width:100%;font-size:12px;justify-content:center;margin-top:8px;color:#EF4444;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                Remover todas
                            </button>
                        @endif

                        @error('newPhotos.*') <span class="nx-field-error" style="margin-top:6px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- RESUMO DO VEÍCULO --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Resumo
                    </div>
                    <div class="nx-sidebar-card-body" style="padding:14px;">
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div>
                                <p style="font-size:10.5px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin:0 0 2px;">Placa</p>
                                <p style="font-size:15px;font-weight:800;color:#1E293B;margin:0;font-family:monospace;letter-spacing:0.1em;text-transform:uppercase;">
                                    {{ $form->plate ?: '—' }}
                                </p>
                            </div>
                            <div>
                                <p style="font-size:10.5px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin:0 0 2px;">Marca / Modelo</p>
                                <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">
                                    {{ trim(($form->brand ?? '') . ' ' . ($form->model ?? '')) ?: '—' }}
                                </p>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div>
                                    <p style="font-size:10.5px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin:0 0 2px;">Ano Fab.</p>
                                    <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">{{ $form->manufacturing_year ?: '—' }}</p>
                                </div>
                                <div>
                                    <p style="font-size:10.5px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin:0 0 2px;">Combustível</p>
                                    <p style="font-size:12px;font-weight:600;color:#334155;margin:0;">
                                        @if($form->fuel_type)
                                            {{ \App\Enums\CombustivelVeiculo::from($form->fuel_type)->label() }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div style="padding-top:10px;border-top:1px solid #F1F5F9;">
                                <p style="font-size:10.5px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin:0 0 4px;">Status</p>
                                @if($form->is_active)
                                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#059669;background:#ECFDF5;padding:4px 12px;border-radius:20px;">
                                        <span style="width:6px;height:6px;background:#10B981;border-radius:50%;display:inline-block;"></span>
                                        Operacional
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#DC2626;background:#FEF2F2;padding:4px 12px;border-radius:20px;">
                                        <span style="width:6px;height:6px;background:#EF4444;border-radius:50%;display:inline-block;"></span>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($isEditing && $vehicle?->created_at)
                    <div class="nx-sidebar-card" style="margin-top:12px;">
                        <div class="nx-sidebar-card-body" style="padding:14px 16px;">
                            <p style="font-size:11px;color:#94A3B8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Cadastrado em</p>
                            <p style="font-size:13px;font-weight:600;color:#475569;margin:0;">{{ $vehicle->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                    </div>
                @endif

            </aside>{{-- /nx-product-sidebar --}}

        </div>{{-- /nx-product-layout --}}

        {{-- FOOTER --}}
        <div class="nx-product-footer" style="max-width:1200px;margin:20px auto 0;">
            <div class="nx-form-footer-left">
                <a href="{{ route('vehicles.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Salvar Veículo' }}
            </button>
        </div>

    </form>
</div>

