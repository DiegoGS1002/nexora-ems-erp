<div class="nx-product-page">

    {{-- ═══════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════ --}}
    <div class="nx-form-header" style="max-width:1200px;margin:0 auto 24px;">
        <a href="{{ route('employees.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Funcionários
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Funcionário' : 'Novo Funcionário' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados do colaborador' : 'Preencha as informações para cadastrar o novo colaborador' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($isEditing)
                    <span class="nx-status-badge nx-status-badge--edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editando
                    </span>
                @endif
                <a href="{{ route('employees.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
                <button form="employee-form" type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-primary">
                    <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Salvar Funcionário' }}
                </button>
            </div>
        </div>
    </div>

    {{-- FLASH / ERRORS --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
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

    <form id="employee-form" wire:submit="save" enctype="multipart/form-data" style="max-width:1200px;margin:0 auto;">

        {{-- ═══════ TABS ═══════ --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key' => 'dados-pessoais',     'label' => 'Dados Pessoais',      'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
                    ['key' => 'dados-profissionais','label' => 'Dados Profissionais', 'icon' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
                    ['key' => 'acesso-sistema',     'label' => 'Acesso ao Sistema',   'icon' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
                    ['key' => 'info-bancarias',     'label' => 'Inf. Bancárias',      'icon' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                    ['key' => 'documentos',         'label' => 'Documentos',          'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                    ['key' => 'observacoes',        'label' => 'Observações',         'icon' => '<line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/>'],
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

                {{-- ══ ABA: DADOS PESSOAIS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'dados-pessoais'])>

                    {{-- INFORMAÇÕES PESSOAIS --}}
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Informações Pessoais</h3>
                                <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Nome Completo <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.name" placeholder="Nome conforme documento oficial" maxlength="255">
                                    @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Nome Social</label>
                                    <input type="text" wire:model.blur="form.social_name" placeholder="Nome de preferência" maxlength="255">
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>CPF <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.identification_number" placeholder="000.000.000-00" maxlength="14">
                                    @error('form.identification_number') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Data de Nascimento</label>
                                    <input type="date" wire:model.blur="form.birth_date">
                                </div>
                            </div>

                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Gênero</label>
                                    <select wire:model="form.gender">
                                        <option value="">Selecione</option>
                                        <option value="feminino">Feminino</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="outros">Outros</option>
                                        <option value="nao_informado">Prefiro não informar</option>
                                    </select>
                                </div>
                                <div class="nx-field">
                                    <label>Estado Civil</label>
                                    <select wire:model="form.marital_status">
                                        <option value="">Selecione</option>
                                        <option value="solteiro">Solteiro(a)</option>
                                        <option value="casado">Casado(a)</option>
                                        <option value="divorciado">Divorciado(a)</option>
                                        <option value="viuvo">Viúvo(a)</option>
                                        <option value="uniao_estavel">União Estável</option>
                                        <option value="separado">Separado(a)</option>
                                    </select>
                                </div>
                                <div class="nx-field">
                                    <label>Nacionalidade</label>
                                    <input type="text" wire:model.blur="form.nationality" placeholder="Ex: Brasileiro(a)">
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>E-mail <span class="nx-required">*</span></label>
                                    <input type="email" wire:model.blur="form.email" placeholder="email@exemplo.com">
                                    @error('form.email') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Telefone Principal <span class="nx-required">*</span></label>
                                    <input type="tel" wire:model.blur="form.phone_number" placeholder="(00) 00000-0000" maxlength="20">
                                    @error('form.phone_number') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Telefone Secundário</label>
                                    <input type="tel" wire:model.blur="form.phone_secondary" placeholder="(00) 00000-0000" maxlength="20">
                                </div>
                                <div class="nx-field">
                                    <label>Naturalidade</label>
                                    <input type="text" wire:model.blur="form.birthplace" placeholder="Cidade e Estado de nascimento">
                                </div>
                            </div>

                            <div class="grid grid-3">
                                <div class="nx-field">
                                    <label>RG</label>
                                    <input type="text" wire:model.blur="form.rg" placeholder="0000000">
                                </div>
                                <div class="nx-field">
                                    <label>Órgão Emissor</label>
                                    <input type="text" wire:model.blur="form.rg_issuer" placeholder="Ex: SSP/MG">
                                </div>
                                <div class="nx-field">
                                    <label>Data de Expedição</label>
                                    <input type="date" wire:model.blur="form.rg_date">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ENDEREÇO --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Endereço</h3>
                                <span class="nx-section-hint">Busca automática pelo CEP via BrasilAPI</span>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>CEP</label>
                                    <div style="display:flex;gap:8px;align-items:flex-end;">
                                        <div style="flex:1;">
                                            <input type="text" wire:model.blur="form.zip_code" placeholder="00000-000" maxlength="9">
                                        </div>
                                        <button type="button" wire:click="buscarCep"
                                            wire:loading.attr="disabled" wire:target="buscarCep"
                                            class="nx-lookup-btn" style="width:auto;padding:10px 14px;flex-shrink:0;">
                                            <svg wire:loading.remove wire:target="buscarCep" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                            <svg wire:loading wire:target="buscarCep" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                            Buscar CEP
                                        </button>
                                    </div>
                                    @if($cepError)
                                        <span class="nx-field-error">{{ $cepError }}</span>
                                    @endif
                                </div>
                                <div class="nx-field">
                                    <label>País</label>
                                    <input type="text" wire:model.blur="form.country" placeholder="Brasil">
                                </div>
                            </div>

                            <div class="grid" style="grid-template-columns:2fr 1fr 1fr;gap:16px;margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Logradouro</label>
                                    <input type="text" wire:model.blur="form.street" placeholder="Rua, Av., Travessa...">
                                </div>
                                <div class="nx-field">
                                    <label>Número</label>
                                    <input type="text" wire:model.blur="form.number" placeholder="Nº">
                                </div>
                                <div class="nx-field">
                                    <label>Complemento</label>
                                    <input type="text" wire:model.blur="form.complement" placeholder="Apto, Bloco...">
                                </div>
                            </div>

                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Bairro</label>
                                    <input type="text" wire:model.blur="form.neighborhood" placeholder="Bairro">
                                </div>
                                <div class="nx-field">
                                    <label>Cidade</label>
                                    <input type="text" wire:model.blur="form.city" placeholder="Cidade">
                                </div>
                                <div class="nx-field">
                                    <label>Estado (UF)</label>
                                    <select wire:model="form.state">
                                        <option value="">UF</option>
                                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                            <option value="{{ $uf }}">{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if($form->street)
                                <div class="nx-field">
                                    <label>Endereço Completo</label>
                                    <input type="text" readonly
                                        value="{{ trim(implode(', ', array_filter([$form->street, $form->number, $form->complement, $form->neighborhood, $form->city, $form->state]))) }}"
                                        style="background:#F8FAFC;color:#64748B;cursor:default;">
                                    <small>Gerado automaticamente para conferência</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- CONTATO DE EMERGÊNCIA --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l.85-.85a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 17z"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Contato de Emergência</h3>
                                <span class="nx-section-hint">Para casos de necessidade médica ou administrativa</span>
                            </div>
                            <div class="grid grid-3">
                                <div class="nx-field">
                                    <label>Nome</label>
                                    <input type="text" wire:model.blur="form.emergency_contact_name" placeholder="Nome completo">
                                </div>
                                <div class="nx-field">
                                    <label>Parentesco</label>
                                    <input type="text" wire:model.blur="form.emergency_contact_relationship" placeholder="Ex: Cônjuge, Pai, Mãe">
                                </div>
                                <div class="nx-field">
                                    <label>Telefone</label>
                                    <input type="tel" wire:model.blur="form.emergency_contact_phone" placeholder="(00) 00000-0000" maxlength="20">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: DADOS PROFISSIONAIS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'dados-profissionais'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Dados Profissionais</h3>
                                <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
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
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="nx-field">
                                    <label>Cargo / Função <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.role" placeholder="Ex: Analista Financeiro">
                                    @error('form.role') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Data de Admissão</label>
                                    <input type="date" wire:model.blur="form.admission_date">
                                </div>
                                <div class="nx-field">
                                    <label>Jornada de Trabalho</label>
                                    <select wire:model="form.work_schedule">
                                        <option value="">Selecione a jornada</option>
                                        <option value="44h_semanais">44h semanais</option>
                                        <option value="40h_semanais">40h semanais</option>
                                        <option value="36h_semanais">36h semanais</option>
                                        <option value="30h_semanais">30h semanais</option>
                                        <option value="meio_periodo">Meio período</option>
                                        <option value="flexivel">Horário flexível</option>
                                        <option value="home_office">Home Office</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-2">
                                <div class="nx-field">
                                    <label>Salário Base (R$)</label>
                                    <div style="position:relative;">
                                        <span class="nx-input-prefix">R$</span>
                                        <input type="number" step="0.01" wire:model.blur="form.salary" placeholder="0,00" style="padding-left:42px;">
                                    </div>
                                </div>
                                <div class="nx-field">
                                    <label>Registro Interno</label>
                                    <input type="text" wire:model.blur="form.internal_code" placeholder="Ex: FUNC-000123"
                                        style="font-family:'JetBrains Mono','Courier New',monospace;font-size:12.5px;letter-spacing:0.04em;">
                                    <small>Código único no Nexora ERP</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: ACESSO AO SISTEMA ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'acesso-sistema'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Permissões e Acesso</h3>
                            </div>

                            <div class="nx-field" style="margin-bottom:16px;">
                                <label>Perfil de Acesso</label>
                                <select wire:model="form.access_profile">
                                    <option value="">Selecione o perfil</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="gerente">Gerente</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="rh">RH</option>
                                    <option value="financeiro">Financeiro</option>
                                    <option value="estoque">Estoque</option>
                                    <option value="producao">Produção</option>
                                    <option value="logistica">Logística</option>
                                    <option value="visualizador">Somente Visualização</option>
                                </select>
                                <small>Define as permissões do colaborador no Nexora ERP</small>
                            </div>

                            <div style="border:1px solid #F1F5F9;border-radius:10px;overflow:hidden;">
                                <label style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px;cursor:pointer;background:#FAFBFD;gap:16px;">
                                    <div class="nx-toggle-info">
                                        <span class="nx-toggle-label">Status do Colaborador</span>
                                        <p class="nx-toggle-desc">{{ $form->is_active ? 'Colaborador ativo no sistema' : 'Colaborador inativo (desligado)' }}</p>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <span style="font-size:12px;font-weight:700;color:{{ $form->is_active ? '#059669' : '#DC2626' }};">{{ $form->is_active ? 'Ativo' : 'Inativo' }}</span>
                                        <span class="nx-switch"><input type="checkbox" wire:model.live="form.is_active"><span class="nx-switch-track"></span></span>
                                    </div>
                                </label>
                                <label style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px;cursor:pointer;gap:16px;border-top:1px solid #F1F5F9;">
                                    <div class="nx-toggle-info">
                                        <span class="nx-toggle-label">Permitir Acesso ao Sistema</span>
                                        <p class="nx-toggle-desc">{{ $form->allow_system_access ? 'Pode fazer login no Nexora ERP' : 'Acesso ao sistema bloqueado' }}</p>
                                    </div>
                                    <span class="nx-switch"><input type="checkbox" wire:model.live="form.allow_system_access"><span class="nx-switch-track"></span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: INFORMAÇÕES BANCÁRIAS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'info-bancarias'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Informações Bancárias</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Módulo bancário em desenvolvimento. Banco, agência, conta e tipo de conta estarão disponíveis em breve.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: DOCUMENTOS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'documentos'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Documentos</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Upload de documentos (CNH, CTPS, diploma, certidões) será disponibilizado em breve.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: OBSERVAÇÕES ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'observacoes'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Observações Internas</h3>
                                <span class="nx-section-hint">Visível apenas para o RH</span>
                            </div>
                            <div class="nx-field">
                                <label>Anotações e observações sobre o colaborador</label>
                                <textarea wire:model.blur="form.observations" rows="8"
                                    placeholder="Registre aqui informações adicionais, histórico, avaliações ou quaisquer anotações relevantes sobre o colaborador..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /nx-product-main --}}

            {{-- ─── SIDEBAR ─── --}}
            <aside class="nx-product-sidebar">

                {{-- FOTO DO FUNCIONÁRIO --}}
                <div class="nx-sidebar-card">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Foto do Funcionário
                    </div>
                    <div class="nx-sidebar-card-body" style="padding:14px;">
                        @if($photo)
                            <div style="position:relative;margin-bottom:10px;">
                                <img src="{{ $photo->temporaryUrl() }}" alt="Foto preview"
                                    style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:10px;border:1px solid #E2E8F0;display:block;">
                                <button type="button" wire:click="$set('photo', null)"
                                    style="position:absolute;top:8px;right:8px;width:28px;height:28px;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.12);color:#64748B;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <button type="button" wire:click="$set('photo', null)" class="nx-btn nx-btn-ghost" style="width:100%;font-size:12px;justify-content:center;">
                                Remover foto
                            </button>
                        @elseif($isEditing && $employee?->photo)
                            <img src="{{ asset('storage/'.$employee->photo) }}" alt="{{ $employee->name }}"
                                style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:10px;border:1px solid #E2E8F0;display:block;margin-bottom:10px;">
                        @else
                            <div class="nx-dropzone" x-data="{dragover:false}"
                                @dragover.prevent="dragover=true" @dragleave="dragover=false" @drop.prevent="dragover=false"
                                :class="dragover && 'nx-dropzone--over'" style="min-height:160px;">
                                <div class="nx-dropzone-placeholder" style="padding:28px 16px;">
                                    <div class="nx-dropzone-icon" style="width:52px;height:52px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <p class="nx-dropzone-label" style="font-size:12.5px;text-align:center;">Arraste ou <strong>clique para selecionar</strong></p>
                                    <p class="nx-dropzone-hint">PNG, JPG, WEBP — até 5MB</p>
                                </div>
                                <input type="file" wire:model="photo" accept="image/png,image/jpeg,image/webp" class="nx-dropzone-input">
                            </div>
                        @endif
                        @error('photo') <span class="nx-field-error" style="margin-top:6px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- ACESSO AO SISTEMA --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Acesso ao Sistema
                    </div>
                    <div class="nx-sidebar-card-body">
                        <div class="nx-field" style="margin-bottom:14px;">
                            <label>Perfil de Acesso</label>
                            <select wire:model="form.access_profile" style="font-size:13px;">
                                <option value="">Selecione o perfil</option>
                                <option value="administrador">Administrador</option>
                                <option value="gerente">Gerente</option>
                                <option value="vendedor">Vendedor</option>
                                <option value="rh">RH</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="estoque">Estoque</option>
                                <option value="producao">Produção</option>
                                <option value="logistica">Logística</option>
                                <option value="visualizador">Visualização</option>
                            </select>
                        </div>

                        <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-top:1px solid #F1F5F9;cursor:pointer;gap:12px;">
                            <div>
                                <span style="font-size:13px;font-weight:600;color:#1E293B;display:block;">Status</span>
                                <span style="font-size:11.5px;font-weight:700;color:{{ $form->is_active ? '#059669' : '#DC2626' }};">{{ $form->is_active ? '● Ativo' : '● Inativo' }}</span>
                            </div>
                            <span class="nx-switch"><input type="checkbox" wire:model.live="form.is_active"><span class="nx-switch-track"></span></span>
                        </label>

                        <div class="nx-field" style="margin-top:12px;padding-top:12px;border-top:1px solid #F1F5F9;">
                            <label style="font-size:11.5px;">Data de Admissão</label>
                            <input type="date" wire:model.blur="form.admission_date" style="font-size:13px;">
                        </div>

                        <div class="nx-field" style="margin-top:10px;">
                            <label style="font-size:11.5px;">Jornada de Trabalho</label>
                            <select wire:model="form.work_schedule" style="font-size:13px;">
                                <option value="">Selecione</option>
                                <option value="44h_semanais">44h semanais</option>
                                <option value="40h_semanais">40h semanais</option>
                                <option value="36h_semanais">36h semanais</option>
                                <option value="30h_semanais">30h semanais</option>
                                <option value="meio_periodo">Meio período</option>
                                <option value="flexivel">Horário flexível</option>
                                <option value="home_office">Home Office</option>
                            </select>
                        </div>

                        <label style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:1px solid #F1F5F9;cursor:pointer;gap:12px;">
                            <div>
                                <span style="font-size:12.5px;font-weight:600;color:#1E293B;display:block;">Permitir Login</span>
                                <span style="font-size:11px;color:#94A3B8;">Acesso ao ERP</span>
                            </div>
                            <span class="nx-switch"><input type="checkbox" wire:model.live="form.allow_system_access"><span class="nx-switch-track"></span></span>
                        </label>
                    </div>
                </div>

                {{-- INFORMAÇÕES ADICIONAIS RH --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Informações RH
                    </div>
                    <div class="nx-sidebar-card-body">
                        <div class="nx-field" style="margin-bottom:10px;">
                            <label style="font-size:11.5px;">Departamento</label>
                            <select wire:model="form.department" style="font-size:13px;">
                                <option value="">Selecione</option>
                                <option value="administrativo">Administrativo</option>
                                <option value="comercial">Comercial</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="logistica">Logística</option>
                                <option value="producao">Produção</option>
                                <option value="rh">Recursos Humanos</option>
                                <option value="ti">TI</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="nx-field" style="margin-bottom:10px;">
                            <label style="font-size:11.5px;">Cargo / Função <span class="nx-required">*</span></label>
                            <input type="text" wire:model.blur="form.role" placeholder="Ex: Analista" style="font-size:13px;">
                            @error('form.role') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field" style="margin-bottom:10px;">
                            <label style="font-size:11.5px;">Salário Base (R$)</label>
                            <div style="position:relative;">
                                <span class="nx-input-prefix" style="font-size:11.5px;">R$</span>
                                <input type="number" step="0.01" wire:model.blur="form.salary" placeholder="0,00" style="padding-left:36px;font-size:13px;">
                            </div>
                        </div>
                        <div class="nx-field">
                            <label style="font-size:11.5px;">Registro Interno</label>
                            <input type="text" wire:model.blur="form.internal_code" placeholder="FUNC-000001"
                                style="font-family:'JetBrains Mono','Courier New',monospace;font-size:12px;letter-spacing:0.04em;">
                            <small>Código único no ERP</small>
                        </div>
                    </div>
                </div>

                @if($isEditing && $employee?->created_at)
                    <div class="nx-sidebar-card" style="margin-top:12px;">
                        <div class="nx-sidebar-card-body" style="padding:14px 16px;">
                            <p style="font-size:11px;color:#94A3B8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Cadastrado em</p>
                            <p style="font-size:13px;font-weight:600;color:#475569;margin:0;">{{ $employee->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                    </div>
                @endif

            </aside>{{-- /nx-product-sidebar --}}

        </div>{{-- /nx-product-layout --}}

        {{-- FOOTER --}}
        <div class="nx-product-footer" style="max-width:1200px;margin:20px auto 0;">
            <div class="nx-form-footer-left">
                <a href="{{ route('employees.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Salvar Funcionário' }}
            </button>
        </div>

    </form>
</div>

