<div class="nx-product-page" x-data="rolesPage()">

    {{-- ═══════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════ --}}
    <div class="nx-form-header" style="max-width:1200px;margin:0 auto 24px;">
        <a href="{{ route('roles.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Funções
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Função' : 'Cadastro de Funções' }}</h1>
                <p class="nx-form-subtitle">Crie e gerencie funções para controlar permissões de acesso ao sistema</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($isEditing)
                    <span class="nx-status-badge nx-status-badge--edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Editando
                    </span>
                @endif
                <a href="{{ route('roles.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
                <button form="role-form" type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="nx-btn--loading"
                    class="nx-btn nx-btn-primary">
                    <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Salvar Função
                </button>
            </div>
        </div>
    </div>

    {{-- FLASH / ERROS --}}
    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    @if ($errors->any())
        <div class="alert-error" style="max-width:1200px;margin:0 auto 16px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul style="margin:6px 0 0 16px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="role-form" wire:submit="save" style="max-width:1200px;margin:0 auto;">

        {{-- ═══════════ ABAS ═══════════ --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key' => 'permissoes',        'label' => 'Permissões',         'icon' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
                    ['key' => 'modulos',            'label' => 'Módulos do Sistema', 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
                    ['key' => 'restricoes',         'label' => 'Restrições de Dados','icon' => '<circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>'],
                    ['key' => 'aprovacoes',         'label' => 'Aprovações',         'icon' => '<polyline points="20 6 9 17 4 12"/>'],
                    ['key' => 'historico',          'label' => 'Histórico',          'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
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

        {{-- ══════ LAYOUT PRINCIPAL ══════ --}}
        <div class="nx-product-layout">

            {{-- ─── COLUNA PRINCIPAL ─── --}}
            <div class="nx-product-main">

                {{-- ══ ABA: PERMISSÕES ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'permissoes'])>

                    {{-- DADOS DA FUNÇÃO --}}
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon" style="background:rgba(139,92,246,0.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Dados da Função</h3>
                                <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Nome da Função <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.name"
                                        placeholder="Ex: Analista Financeiro" maxlength="255"
                                        style="font-weight:500;">
                                    @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Departamento <span class="nx-required">*</span></label>
                                    <select wire:model="form.department">
                                        <option value="">Selecione o departamento</option>
                                        <option value="Administrativo">Administrativo</option>
                                        <option value="Comercial">Comercial</option>
                                        <option value="Compras">Compras</option>
                                        <option value="Financeiro">Financeiro</option>
                                        <option value="Fiscal">Fiscal</option>
                                        <option value="Logística">Logística</option>
                                        <option value="Produção">Produção</option>
                                        <option value="Recursos Humanos">Recursos Humanos</option>
                                        <option value="TI">TI</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                    @error('form.department') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Código <span class="nx-required">*</span></label>
                                    <input type="text" wire:model.blur="form.code"
                                        placeholder="Ex: FIN-ANL" maxlength="50"
                                        style="font-family:monospace;font-size:13px;letter-spacing:0.05em;text-transform:uppercase;">
                                    <small>Identificador único (apenas letras maiúsculas, números e hífen)</small>
                                    @error('form.code') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Função Superior</label>
                                    <select wire:model.live="form.parent_role_id">
                                        <option value="">Nenhuma (função raiz)</option>
                                        @foreach($this->otherRoles as $r)
                                            <option value="{{ $r->id }}">
                                                {{ $r->name }}{{ $r->department ? ' — '.$r->department : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small>Herda permissões da função selecionada</small>
                                </div>
                            </div>

                            <div class="nx-field">
                                <label>
                                    Descrição
                                    <span style="font-size:11.5px;color:#94A3B8;font-weight:400;margin-left:6px;" wire:ignore.self>
                                        <span wire:key="desc-count">{{ strlen($form->description) }}</span>/300
                                    </span>
                                </label>
                                <textarea wire:model.live="form.description" rows="3"
                                    placeholder="Descreva as responsabilidades desta função..."
                                    maxlength="300"
                                    style="resize:vertical;"></textarea>
                                @error('form.description') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- GERENCIAMENTO DE PERMISSÕES --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;padding:0;">
                            <div class="nx-form-section-header" style="padding:16px 20px 14px;">
                                <div class="nx-form-section-icon" style="background:rgba(59,130,246,0.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Permissões por Módulo</h3>
                                <div style="margin-left:auto;display:flex;gap:8px;">
                                    <button type="button" wire:click="expandAll"
                                        class="nx-roles-action-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>
                                        Expandir Todos
                                    </button>
                                    <button type="button" wire:click="collapseAll"
                                        class="nx-roles-action-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 11 12 6 17 11"/><polyline points="7 18 12 13 17 18"/></svg>
                                        Recolher Todos
                                    </button>
                                </div>
                            </div>

                            {{-- CABEÇALHO DA TABELA DE PERMISSÕES --}}
                            <div class="nx-perm-table-header">
                                <div class="nx-perm-module-col">Módulo</div>
                                @foreach($actions as $actionKey => $actionLabel)
                                    <div class="nx-perm-action-col">{{ $actionLabel }}</div>
                                @endforeach
                            </div>

                            {{-- ACORDEÕES DE MÓDULOS --}}
                            <div class="nx-perm-accordion">
                                @foreach($modules as $moduleKey => $moduleName)
                                    @php
                                        $isExpanded = in_array($moduleKey, $expandedModules);
                                        $modulePerms = $form->permissions[$moduleKey] ?? [];
                                        $activeCount = collect($modulePerms)->filter()->count();
                                        $totalActions = count($actions);
                                    @endphp
                                    <div class="nx-perm-module" wire:key="module-{{ $moduleKey }}">

                                        {{-- CABEÇALHO DO MÓDULO --}}
                                        <button type="button"
                                            wire:click="toggleModule('{{ $moduleKey }}')"
                                            class="nx-perm-module-header {{ $isExpanded ? 'nx-perm-module-header--open' : '' }}">
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <svg class="nx-perm-chevron {{ $isExpanded ? 'nx-perm-chevron--open' : '' }}"
                                                    xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"/>
                                                </svg>
                                                <span class="nx-perm-module-name">{{ $moduleName }}</span>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                @if($activeCount > 0)
                                                    <span class="nx-perm-count-badge">{{ $activeCount }}/{{ $totalActions }}</span>
                                                @endif
                                                <div class="nx-perm-mini-dots">
                                                    @foreach($actions as $actionKey => $actionLabel)
                                                        <span class="nx-perm-dot {{ ($modulePerms[$actionKey] ?? false) ? 'nx-perm-dot--on' : 'nx-perm-dot--off' }}"
                                                            title="{{ $actionLabel }}"></span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </button>

                                        {{-- GRID DE CHECKBOXES --}}
                                        @if($isExpanded)
                                            <div class="nx-perm-module-body">
                                                {{-- Botões rápidos por módulo --}}
                                                <div style="display:flex;gap:6px;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #F1F5F9;">
                                                    <button type="button"
                                                        wire:click="selectAllModule('{{ $moduleKey }}')"
                                                        class="nx-roles-action-btn"
                                                        style="font-size:11px;padding:3px 9px;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                        Marcar todos
                                                    </button>
                                                    <button type="button"
                                                        wire:click="clearModule('{{ $moduleKey }}')"
                                                        class="nx-roles-action-btn"
                                                        style="font-size:11px;padding:3px 9px;color:#EF4444;border-color:#FEE2E2;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                        Desmarcar todos
                                                    </button>
                                                </div>
                                                <div class="nx-perm-grid">
                                                    @foreach($actions as $actionKey => $actionLabel)
                                                        <label class="nx-perm-checkbox-label"
                                                            wire:key="perm-{{ $moduleKey }}-{{ $actionKey }}">
                                                            <input type="checkbox"
                                                                wire:model.live="form.permissions.{{ $moduleKey }}.{{ $actionKey }}"
                                                                class="nx-perm-checkbox">
                                                            <span class="nx-perm-checkmark"></span>
                                                            <span class="nx-perm-action-name">{{ $actionLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>

                            {{-- NOTA INFORMATIVA --}}
                            <div class="nx-perm-info-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;color:#3B82F6;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                As permissões são aplicadas em tempo real após salvar. Funcionários com esta função terão acesso atualizado automaticamente.
                            </div>

                        </div>
                    </div>

                </div>{{-- /aba permissoes --}}

                {{-- ══ ABA: MÓDULOS DO SISTEMA ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'modulos'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon" style="background:rgba(16,185,129,0.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Módulos Acessíveis</h3>
                            </div>
                            <div class="nx-roles-modules-grid">
                                @foreach($modules as $moduleKey => $moduleName)
                                    @php
                                        $modulePerms = $form->permissions[$moduleKey] ?? [];
                                        $hasAny = collect($modulePerms)->filter()->isNotEmpty();
                                        $count = collect($modulePerms)->filter()->count();
                                    @endphp
                                    <div class="nx-roles-module-card {{ $hasAny ? 'nx-roles-module-card--enabled' : '' }}">
                                        <div class="nx-roles-module-card-top">
                                            <span class="nx-roles-module-status {{ $hasAny ? 'nx-roles-module-status--on' : 'nx-roles-module-status--off' }}"></span>
                                            <span class="nx-roles-module-name">{{ $moduleName }}</span>
                                        </div>
                                        <p class="nx-roles-module-perms">
                                            @if($hasAny)
                                                {{ $count }} de {{ count($actions) }} permissões ativas
                                            @else
                                                Sem acesso configurado
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>{{-- /aba modulos --}}

                {{-- ══ ABA: RESTRIÇÕES DE DADOS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'restricoes'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon" style="background:rgba(239,68,68,0.08);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Restrições de Dados</h3>
                            </div>
                            <div style="padding:20px;text-align:center;color:#94A3B8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:0.4;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                <p style="font-size:13.5px;font-weight:600;color:#64748B;margin-bottom:4px;">Em desenvolvimento</p>
                                <p style="font-size:12.5px;">Configurações de restrição de dados por função serão disponibilizadas em breve.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: APROVAÇÕES ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'aprovacoes'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon" style="background:rgba(245,158,11,0.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Fluxos de Aprovação</h3>
                            </div>
                            <div style="padding:20px;text-align:center;color:#94A3B8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:0.4;"><polyline points="20 6 9 17 4 12"/></svg>
                                <p style="font-size:13.5px;font-weight:600;color:#64748B;margin-bottom:4px;">Em desenvolvimento</p>
                                <p style="font-size:12.5px;">Configurações de fluxos de aprovação serão disponibilizadas em breve.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: HISTÓRICO ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'historico'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon" style="background:rgba(100,116,139,0.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Histórico de Alterações</h3>
                            </div>
                            @if($role?->created_at)
                                <div style="padding:20px;">
                                    <div class="nx-roles-history-item">
                                        <div class="nx-roles-history-dot nx-roles-history-dot--create"></div>
                                        <div>
                                            <p style="font-size:13px;font-weight:600;color:#1E293B;margin:0 0 2px;">Função criada</p>
                                            <p style="font-size:12px;color:#94A3B8;margin:0;">{{ $role->created_at->format('d/m/Y \à\s H:i') }}</p>
                                        </div>
                                    </div>
                                    @if($role->updated_at && $role->updated_at->ne($role->created_at))
                                        <div class="nx-roles-history-item">
                                            <div class="nx-roles-history-dot nx-roles-history-dot--update"></div>
                                            <div>
                                                <p style="font-size:13px;font-weight:600;color:#1E293B;margin:0 0 2px;">Última atualização</p>
                                                <p style="font-size:12px;color:#94A3B8;margin:0;">{{ $role->updated_at->format('d/m/Y \à\s H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div style="padding:20px;text-align:center;color:#94A3B8;">
                                    <p style="font-size:13px;">Nenhum histórico disponível para esta função.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>{{-- /nx-product-main --}}

            {{-- ─── SIDEBAR ─── --}}
            <aside class="nx-product-sidebar">

                {{-- STATUS E CONFIGURAÇÕES --}}
                <div class="nx-sidebar-card">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                        Status e Configurações
                    </div>
                    <div class="nx-sidebar-card-body">

                        {{-- STATUS TOGGLE --}}
                        <div class="nx-roles-status-row">
                            <div>
                                <p style="font-size:12.5px;font-weight:600;color:#1E293B;margin:0 0 2px;">Status <span class="nx-required">*</span></p>
                                <p style="font-size:11.5px;color:#94A3B8;margin:0;">Define se a função está ativa</p>
                            </div>
                            <label class="nx-roles-status-toggle">
                                <input type="checkbox" wire:model.live="form.is_active">
                                <span class="nx-roles-status-track"
                                    :class="$wire.form.is_active ? 'nx-roles-status-track--on' : 'nx-roles-status-track--off'">
                                    <span class="nx-roles-status-thumb"></span>
                                    <span class="nx-roles-status-label">
                                        {{ $form->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </span>
                            </label>
                        </div>

                        {{-- PERMITIR ATRIBUIÇÃO --}}
                        <label class="nx-toggle-row" style="margin-top:10px;cursor:pointer;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">Permitir Atribuição</span>
                                <span class="nx-toggle-desc">Permite atribuir esta função a funcionários</span>
                            </div>
                            <span class="nx-switch">
                                <input type="checkbox" wire:model.live="form.allow_assignment">
                                <span class="nx-switch-track"></span>
                            </span>
                        </label>

                        {{-- USUÁRIOS COM ESTA FUNÇÃO --}}
                        @if($isEditing)
                            <div class="nx-roles-users-card">
                                <div class="nx-roles-users-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div>
                                    <p class="nx-roles-users-count">{{ $this->employeesCount }}</p>
                                    <p class="nx-roles-users-label">funcionário(s) com esta função</p>
                                </div>
                                <a href="{{ route('employees.index') }}" class="nx-roles-users-link" wire:navigate title="Ver detalhes">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- RESUMO DE PERMISSÕES --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                        Resumo de Permissões
                    </div>
                    <div class="nx-sidebar-card-body">

                        {{-- DONUT CHART --}}
                        @php
                            $summary = $this->permissionsSummary;
                            $total = $summary['total'];
                            $allowed = $summary['allowed'];
                            $unconfigured = $summary['unconfigured'];
                            $pct = $total > 0 ? round(($allowed / $total) * 100) : 0;
                            $circumference = 2 * 3.14159 * 36;
                            $dashAllowed = ($total > 0) ? ($allowed / $total) * $circumference : 0;
                            $dashUnconfigured = $circumference - $dashAllowed;
                        @endphp

                        <div class="nx-roles-donut-wrap">
                            <svg viewBox="0 0 88 88" class="nx-roles-donut" style="width:120px;height:120px;">
                                {{-- Fundo cinza --}}
                                <circle cx="44" cy="44" r="36"
                                    fill="none" stroke="#E2E8F0" stroke-width="10"/>
                                {{-- Arco verde (permitidas) --}}
                                <circle cx="44" cy="44" r="36"
                                    fill="none" stroke="#10B981" stroke-width="10"
                                    stroke-dasharray="{{ $dashAllowed }} {{ $dashUnconfigured }}"
                                    stroke-dashoffset="{{ $circumference * 0.25 }}"
                                    stroke-linecap="round"
                                    style="transition:stroke-dasharray 0.4s ease;"/>
                                <text x="44" y="40" text-anchor="middle"
                                    font-family="Inter,sans-serif" font-size="16" font-weight="700" fill="#1E293B">
                                    {{ $pct }}%
                                </text>
                                <text x="44" y="54" text-anchor="middle"
                                    font-family="Inter,sans-serif" font-size="7" fill="#94A3B8">
                                    configurado
                                </text>
                            </svg>
                        </div>

                        <div class="nx-roles-legend">
                            <div class="nx-roles-legend-item">
                                <span class="nx-roles-legend-dot" style="background:#10B981;"></span>
                                <span class="nx-roles-legend-label">Permitidas</span>
                                <span class="nx-roles-legend-value">{{ $allowed }}</span>
                            </div>
                            <div class="nx-roles-legend-item">
                                <span class="nx-roles-legend-dot" style="background:#E2E8F0;border:1px solid #CBD5E1;"></span>
                                <span class="nx-roles-legend-label">Não configuradas</span>
                                <span class="nx-roles-legend-value">{{ $unconfigured }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- AÇÕES RÁPIDAS --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Ações Rápidas
                    </div>
                    <div class="nx-sidebar-card-body" style="padding:12px;">
                        <button type="button" wire:click="selectAll"
                            class="nx-roles-quick-btn nx-roles-quick-btn--green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Selecionar Todos
                        </button>
                        <button type="button" wire:click="clearAll"
                            class="nx-roles-quick-btn nx-roles-quick-btn--red">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Limpar Seleção
                        </button>
                        <button type="button" wire:click="openCopyModal"
                            class="nx-roles-quick-btn nx-roles-quick-btn--blue">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            Copiar de Outra Função
                        </button>
                    </div>
                </div>

                {{-- DICA DE SEGURANÇA --}}
                <div class="nx-roles-security-tip" style="margin-top:12px;">
                    <div class="nx-roles-security-tip-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <p class="nx-roles-security-tip-title">Princípio do Menor Privilégio</p>
                        <p class="nx-roles-security-tip-text">Garanta que usuários tenham apenas os acessos estritamente necessários para suas atividades.</p>
                    </div>
                </div>

                {{-- INFO DE CRIAÇÃO --}}
                @if($isEditing && $role?->created_at)
                    <div class="nx-sidebar-card" style="margin-top:12px;">
                        <div class="nx-sidebar-card-body" style="padding:14px 16px;">
                            <p style="font-size:11px;color:#94A3B8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Cadastrado em</p>
                            <p style="font-size:13px;font-weight:600;color:#475569;margin:0;">{{ $role->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                    </div>
                @endif

            </aside>{{-- /nx-product-sidebar --}}

        </div>{{-- /nx-product-layout --}}

        {{-- FOOTER --}}
        <div class="nx-product-footer" style="max-width:1200px;margin:20px auto 0;">
            <div class="nx-form-footer-left">
                <a href="{{ route('roles.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            </div>
            <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="nx-btn--loading"
                class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Salvar Função
            </button>
        </div>

    </form>

    {{-- ═══════════════════════════════════════════════
         MODAL: Copiar Permissões
         ═══════════════════════════════════════════════ --}}
    @if($showCopyModal)
        <div class="nx-modal-overlay" wire:click.self="closeCopyModal">
            <div class="nx-modal nx-modal--sm">
                <div class="nx-modal-header">
                    <h3 class="nx-modal-title">Copiar Permissões</h3>
                    <button type="button" wire:click="closeCopyModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <p style="font-size:13.5px;color:#475569;margin-bottom:14px;">Selecione a função de origem. As permissões serão copiadas para esta função.</p>
                    <div class="nx-field">
                        <label>Copiar de</label>
                        <select wire:model.live="copyFromRoleId">
                            <option value="">Selecione uma função...</option>
                            @foreach($this->otherRoles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}{{ $r->department ? ' — '.$r->department : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="closeCopyModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="copyPermissions" class="nx-btn nx-btn-primary"
                        @if(!$copyFromRoleId) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copiar Permissões
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

@push('styles')
<style>
/* Alpine.js component */
</style>
@endpush

<script>
function rolesPage() {
    return {};
}
</script>

