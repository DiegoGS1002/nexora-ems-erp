<div class="nx-form-page">

    {{-- HEADER --}}
    <div class="nx-form-header">
        <a href="{{ route('companies.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Voltar para Empresas
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Empresa' : 'Nova Empresa' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados da empresa' : 'Preencha os dados para cadastrar a nova empresa' }}</p>
            </div>
            @if($isEditing)
                <span class="nx-status-badge nx-status-badge--edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Editando
                </span>
            @endif
        </div>
    </div>

    {{-- ERROS --}}
    @if ($errors->any())
        <div class="alert-error" style="margin-bottom:20px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form wire:submit="save" autocomplete="off">

        {{-- ── IDENTIFICAÇÃO ── --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Identificação</h3>
                    <span class="nx-section-hint">Consulte o CNPJ para preencher automaticamente</span>
                </div>

                {{-- CNPJ + Botão de busca --}}
                <div class="grid grid-2" style="margin-bottom:16px;align-items:end;">
                    <div class="nx-field">
                        <label>CNPJ</label>
                        <div style="position:relative;">
                            <input type="text" wire:model.live="form.cnpj"
                                   placeholder="00.000.000/0000-00" maxlength="18" autocomplete="off"
                                   style="padding-right: 36px;">
                            {{-- Spinner de auto-busca --}}
                            <span wire:loading wire:target="updatedFormCnpj"
                                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94A3B8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                            </span>
                        </div>
                        <small style="color:#94A3B8;">Preenchimento automático ao digitar o CNPJ completo</small>
                        @error('form.cnpj') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field" style="align-self:end;">
                        @php $cnpjDigits = strlen(preg_replace('/\D/', '', $form->cnpj ?? '')); @endphp
                        <button type="button" wire:click="buscarCnpj" wire:loading.attr="disabled"
                                wire:target="buscarCnpj,updatedFormCnpj"
                                class="nx-lookup-btn"
                                @if($cnpjDigits < 14) disabled title="Digite um CNPJ completo (14 dígitos)" @endif
                                style="{{ $cnpjDigits < 14 ? 'opacity:0.45;cursor:not-allowed;' : '' }}">
                            <span wire:loading.remove wire:target="buscarCnpj,updatedFormCnpj"
                                  style="display:inline-flex;align-items:center;gap:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                Consultar CNPJ
                            </span>
                            <span wire:loading wire:target="buscarCnpj,updatedFormCnpj"
                                  style="display:inline-flex;align-items:center;gap:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                                Consultando...
                            </span>
                        </button>
                    </div>
                </div>

                @if($cnpjError)
                    <div style="margin-bottom:14px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;color:#DC2626;font-size:12.5px;">
                        {{ $cnpjError }}
                    </div>
                @endif

                <div class="grid grid-2" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Nome Fantasia <span class="nx-required">*</span></label>
                        <input type="text" wire:model="form.name" placeholder="Nome da empresa" autocomplete="off">
                        @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Razão Social</label>
                        <input type="text" wire:model="form.social_name" placeholder="Razão social completa" autocomplete="off">
                        @error('form.social_name') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-3" style="margin-bottom:0;">
                    <div class="nx-field">
                        <label>Inscrição Estadual</label>
                        <input type="text" wire:model="form.inscricao_estadual" placeholder="IE" autocomplete="off">
                        @error('form.inscricao_estadual') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Inscrição Municipal</label>
                        <input type="text" wire:model="form.inscricao_municipal" placeholder="IM" autocomplete="off">
                        @error('form.inscricao_municipal') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Segmento / Ramo</label>
                        <input type="text" wire:model="form.segment" placeholder="Ex: Tecnologia, Varejo..." autocomplete="off">
                        @error('form.segment') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CONTATO ── --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.44-.45a2 2 0 0 1 2.11.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2z"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Contato</h3>
                </div>

                <div class="grid grid-3">
                    <div class="nx-field">
                        <label>E-mail</label>
                        <input type="email" wire:model="form.email" placeholder="contato@empresa.com.br" autocomplete="off">
                        @error('form.email') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Telefone</label>
                        <input type="text" wire:model="form.phone" placeholder="(00) 00000-0000" autocomplete="off">
                        @error('form.phone') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Website</label>
                        <input type="url" wire:model="form.website" placeholder="https://www.empresa.com.br" autocomplete="off">
                        @error('form.website') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ENDEREÇO ── --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Endereço</h3>
                    <span class="nx-section-hint">Digite o CEP para preencher automaticamente</span>
                </div>

                {{-- CEP + busca --}}
                <div class="grid grid-3" style="margin-bottom:16px;align-items:end;">
                    <div class="nx-field">
                        <label>CEP</label>
                        <div style="position:relative;">
                            <input type="text" wire:model.live="form.address_zip_code"
                                   placeholder="00000-000" maxlength="9" autocomplete="off"
                                   style="padding-right: 36px;">
                            {{-- Spinner de auto-busca --}}
                            <span wire:loading wire:target="updatedFormAddressZipCode"
                                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94A3B8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                            </span>
                        </div>
                        <small style="color:#94A3B8;">Preenchimento automático ao digitar o CEP completo</small>
                        @error('form.address_zip_code') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field" style="align-self:end;">
                        @php $cepDigits = strlen(preg_replace('/\D/', '', $form->address_zip_code ?? '')); @endphp
                        <button type="button" wire:click="buscarCep" wire:loading.attr="disabled"
                                wire:target="buscarCep,updatedFormAddressZipCode"
                                class="nx-lookup-btn"
                                @if($cepDigits < 8) disabled title="Digite um CEP completo (8 dígitos)" @endif
                                style="{{ $cepDigits < 8 ? 'opacity:0.45;cursor:not-allowed;' : '' }}">
                            <span wire:loading.remove wire:target="buscarCep,updatedFormAddressZipCode"
                                  style="display:inline-flex;align-items:center;gap:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                Buscar CEP
                            </span>
                            <span wire:loading wire:target="buscarCep,updatedFormAddressZipCode"
                                  style="display:inline-flex;align-items:center;gap:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                                Buscando...
                            </span>
                        </button>
                    </div>
                </div>

                @if($cepError)
                    <div style="margin-bottom:14px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;color:#DC2626;font-size:12.5px;">
                        {{ $cepError }}
                    </div>
                @endif

                <div class="grid grid-3" style="margin-bottom:16px;">
                    <div class="nx-field" style="grid-column:span 2;">
                        <label>Logradouro</label>
                        <input type="text" wire:model="form.address_street" placeholder="Rua, Avenida..." autocomplete="off">
                        @error('form.address_street') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Número</label>
                        <input type="text" wire:model="form.address_number" placeholder="Nº" autocomplete="off">
                        @error('form.address_number') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-4">
                    <div class="nx-field">
                        <label>Complemento</label>
                        <input type="text" wire:model="form.address_complement" placeholder="Sala, Andar..." autocomplete="off">
                        @error('form.address_complement') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Bairro</label>
                        <input type="text" wire:model="form.address_district" placeholder="Bairro" autocomplete="off">
                        @error('form.address_district') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Cidade</label>
                        <input type="text" wire:model="form.address_city" placeholder="Cidade" autocomplete="off">
                        @error('form.address_city') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field">
                        <label>Estado (UF)</label>
                        <select wire:model="form.address_state">
                            <option value="">UF</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}">{{ $uf }}</option>
                            @endforeach
                        </select>
                        @error('form.address_state') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STATUS E OBSERVAÇÕES ── --}}
        <div class="nx-form-card" style="margin-bottom:16px;">
            <div class="nx-form-section" style="border-bottom:none;">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Status e Observações</h3>
                </div>

                <div class="grid grid-2">
                    {{-- Status --}}
                    <div class="nx-field">
                        <label>Status da Empresa</label>
                        <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
                            <label for="status_ativo" style="
                                display:flex;align-items:center;gap:12px;padding:12px 16px;
                                border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;
                                transition:border-color 0.18s;background:#F8FAFC;">
                                <input type="radio" id="status_ativo" wire:model.live="form.is_active"
                                       value="1" style="width:auto;accent-color:#10B981;">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#059669;">● Ativa</div>
                                    <div style="font-size:12px;color:#64748B;">Empresa habilitada no sistema</div>
                                </div>
                            </label>
                            <label for="status_inativo" style="
                                display:flex;align-items:center;gap:12px;padding:12px 16px;
                                border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;
                                transition:border-color 0.18s;">
                                <input type="radio" id="status_inativo" wire:model.live="form.is_active"
                                       value="0" style="width:auto;accent-color:#EF4444;">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#EF4444;">● Inativa</div>
                                    <div style="font-size:12px;color:#64748B;">Empresa desabilitada no sistema</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div class="nx-field">
                        <label>Observações</label>
                        <textarea wire:model="form.notes" rows="5"
                                  placeholder="Notas internas sobre a empresa..."
                                  style="resize:vertical;"></textarea>
                        @error('form.notes') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="nx-form-footer">
            <a href="{{ route('companies.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save" style="display:inline-flex;align-items:center;gap:7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    {{ $isEditing ? 'Salvar Alterações' : 'Cadastrar Empresa' }}
                </span>
                <span wire:loading wire:target="save" style="display:inline-flex;align-items:center;gap:7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" class="nx-spin">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    Salvando...
                </span>
            </button>
        </div>

    </form>
</div>

