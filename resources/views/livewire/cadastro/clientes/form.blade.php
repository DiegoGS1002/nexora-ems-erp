<div class="nx-form-page">

    {{-- HEADER --}}
    <div class="nx-form-header">
        <a href="{{ route('clients.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Clientes
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Cliente' : 'Novo Cliente' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados do cadastro' : 'Preencha os dados para cadastrar o novo cliente' }}</p>
            </div>
            @if($isEditing)
                <span class="nx-status-badge nx-status-badge--edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editando
                </span>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-error" style="margin-bottom:20px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form wire:submit="save" autocomplete="off">

        {{-- TIPO DE CADASTRO --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Tipo de Cadastro</h3>
                </div>
                <div class="nx-tipo-selector">
                    <label class="nx-tipo-card {{ $form->tipo_pessoa === 'PJ' ? 'nx-tipo-card--active' : '' }}">
                        <input type="radio" wire:model.live="form.tipo_pessoa" value="PJ" class="sr-only">
                        <div class="nx-tipo-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        </div>
                        <div class="nx-tipo-card-text">
                            <span class="nx-tipo-card-label">Pessoa Jurídica</span>
                            <span class="nx-tipo-card-sub">CNPJ — Empresa, MEI, Associação</span>
                        </div>
                        <div class="nx-tipo-card-check {{ $form->tipo_pessoa === 'PJ' ? 'nx-tipo-card-check--active' : '' }}">
                            @if($form->tipo_pessoa === 'PJ')
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </div>
                    </label>
                    <label class="nx-tipo-card {{ $form->tipo_pessoa === 'PF' ? 'nx-tipo-card--active' : '' }}">
                        <input type="radio" wire:model.live="form.tipo_pessoa" value="PF" class="sr-only">
                        <div class="nx-tipo-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="nx-tipo-card-text">
                            <span class="nx-tipo-card-label">Pessoa Física</span>
                            <span class="nx-tipo-card-sub">CPF — Consumidor individual</span>
                        </div>
                        <div class="nx-tipo-card-check {{ $form->tipo_pessoa === 'PF' ? 'nx-tipo-card-check--active' : '' }}">
                            @if($form->tipo_pessoa === 'PF')
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- IDENTIFICAÇÃO --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Identificação</h3>
                    @if($form->tipo_pessoa === 'PJ')
                        <span class="nx-section-hint">Consulte o CNPJ para preencher automaticamente</span>
                    @endif
                </div>

                <div class="grid grid-2" style="margin-bottom:16px; align-items:end;">
                    <div class="nx-field">
                        <label>{{ $form->tipo_pessoa === 'PJ' ? 'CNPJ' : 'CPF' }} <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form.taxNumber"
                            placeholder="{{ $form->tipo_pessoa === 'PJ' ? '00.000.000/0000-00' : '000.000.000-00' }}"
                            maxlength="{{ $form->tipo_pessoa === 'PJ' ? '18' : '14' }}" autocomplete="off">
                        @error('form.taxNumber') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    @if($form->tipo_pessoa === 'PJ')
                        <div class="nx-field" style="align-self:end;">
                            <button type="button" wire:click="buscarCnpj" wire:loading.attr="disabled" wire:target="buscarCnpj" class="nx-lookup-btn">
                                <span wire:loading.remove wire:target="buscarCnpj" style="display:inline-flex;align-items:center;gap:7px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    Consultar CNPJ
                                </span>
                                <span wire:loading wire:target="buscarCnpj" style="display:none;" wire:loading.style="display:inline-flex;align-items:center;gap:7px;">
                                    <svg class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                    Consultando...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>

                @if($cnpjError)
                    <div class="nx-inline-alert nx-inline-alert--warn" style="margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ $cnpjError }}</span>
                    </div>
                @endif

                @if($cnpjSituacao && $cnpjSituacao !== 'ATIVA')
                    <div class="nx-inline-alert nx-inline-alert--danger" style="margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span><strong>Atenção:</strong> Situação Cadastral <strong>{{ $cnpjSituacao }}</strong>. Verifique antes de prosseguir.</span>
                    </div>
                @elseif($cnpjSituacao === 'ATIVA')
                    <div class="nx-inline-alert nx-inline-alert--success" style="margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>CNPJ com situação <strong>ATIVA</strong>@if($cnpjAtividade) — <em>{{ \Illuminate\Support\Str::limit($cnpjAtividade, 80) }}</em>@endif</span>
                    </div>
                @endif

                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>{{ $form->tipo_pessoa === 'PJ' ? 'Nome Fantasia' : 'Nome Completo' }} <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form.name"
                            placeholder="{{ $form->tipo_pessoa === 'PJ' ? 'Nome comercial da empresa' : 'Nome completo do cliente' }}" autocomplete="off">
                        @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    @if($form->tipo_pessoa === 'PJ')
                        <div class="nx-field">
                            <label>Razão Social</label>
                            <input type="text" wire:model="form.social_name" placeholder="Razão Social registrada" autocomplete="off">
                            @error('form.social_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CONTATO --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l.85-.85a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 17z"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Informações de Contato</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>E-mail <span class="nx-required">*</span></label>
                        <input type="email" wire:model="form.email" placeholder="email@exemplo.com" autocomplete="off">
                        @error('form.email') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Telefone / Celular</label>
                        <input type="text" wire:model="form.phone_number" placeholder="(00) 00000-0000" maxlength="20">
                        @error('form.phone_number') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ENDEREÇO --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Endereço</h3>
                    <span class="nx-section-hint">Informe o CEP para preenchimento automático</span>
                </div>

                <div class="grid grid-2" style="margin-bottom:16px; align-items:end;">
                    <div class="nx-field">
                        <label>CEP</label>
                        <input type="text" wire:model="form.address_zip_code" placeholder="00000-000" maxlength="9" autocomplete="off">
                        @error('form.address_zip_code') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field" style="align-self:end;">
                        <button type="button" wire:click="buscarCep" wire:loading.attr="disabled" wire:target="buscarCep" class="nx-lookup-btn nx-lookup-btn--secondary">
                            <span wire:loading.remove wire:target="buscarCep" style="display:inline-flex;align-items:center;gap:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                Buscar CEP
                            </span>
                            <span wire:loading wire:target="buscarCep" style="display:none;" wire:loading.style="display:inline-flex;align-items:center;gap:7px;">
                                <svg class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                Buscando...
                            </span>
                        </button>
                    </div>
                </div>

                @if($cepError)
                    <div class="nx-inline-alert nx-inline-alert--warn" style="margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ $cepError }}</span>
                    </div>
                @endif

                <div class="grid grid-2" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Logradouro</label>
                        <input type="text" wire:model="form.address_street" placeholder="Rua, Avenida, etc.">
                        @error('form.address_street') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Número</label>
                        <input type="text" wire:model="form.address_number" placeholder="Nº" maxlength="20">
                        @error('form.address_number') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-2" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Complemento</label>
                        <input type="text" wire:model="form.address_complement" placeholder="Apto, sala, bloco..." maxlength="100">
                        @error('form.address_complement') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Bairro</label>
                        <input type="text" wire:model="form.address_district" placeholder="Bairro" maxlength="100">
                        @error('form.address_district') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Cidade</label>
                        <input type="text" wire:model="form.address_city" placeholder="Cidade" maxlength="100">
                        @error('form.address_city') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>UF</label>
                        <input type="text" wire:model="form.address_state" placeholder="SP" maxlength="2" style="text-transform:uppercase;">
                        @error('form.address_state') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="nx-form-footer">
            <a href="{{ route('clients.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save" style="display:inline-flex;align-items:center;gap:7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Cadastrar Cliente' }}
                </span>
                <span wire:loading wire:target="save" style="display:none;" wire:loading.style="display:inline-flex;align-items:center;gap:7px;">
                    <svg class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Salvando...
                </span>
            </button>
        </div>

    </form>

</div>

