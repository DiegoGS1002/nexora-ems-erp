<div class="nx-list-page">

    {{-- Cabeçalho --}}
    <div class="nx-form-header" style="margin-bottom:24px;">
        <a href="{{ route('fiscal.tipo-operacao.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Tipos de Operação
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Tipo de Operação' : 'Novo Tipo de Operação' }}</h1>
                <p class="nx-form-subtitle">Configure CFOP, ICMS, IPI, PIS e COFINS para esta operação fiscal</p>
            </div>
            @if($isEditing)
                <span class="nx-status-badge nx-status-badge--edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editando
                </span>
            @endif
        </div>
    </div>

    @session('success')
        <div class="alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession

    @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;">
            <strong>Corrija os erros abaixo:</strong>
            <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form wire:submit="save">

        {{-- ── Abas de navegação ────────────────────────── --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key' => 'geral',      'label' => 'Geral',        'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                    ['key' => 'cfop',       'label' => 'CFOP',         'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
                    ['key' => 'icms',       'label' => 'ICMS',         'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
                    ['key' => 'ipi',        'label' => 'IPI',          'icon' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
                    ['key' => 'pis-cofins', 'label' => 'PIS / COFINS', 'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button type="button" wire:click="$set('activeTab', '{{ $tab['key'] }}')"
                    class="nx-product-tab {{ $activeTab === $tab['key'] ? 'nx-product-tab--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $tab['icon'] !!}</svg>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: GERAL                                            --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'geral'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">Identificação da Operação</h3>
                        <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                    </div>

                    <div class="grid grid-2" style="margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Código <span class="nx-required">*</span></label>
                            <input type="text" wire:model.blur="form.codigo" placeholder="Ex: VENDA-EST, COMPRA-INT..." maxlength="20" style="text-transform:uppercase;">
                            <span style="font-size:11px;color:#94A3B8;">Código único para identificar a operação (máx. 20 car.)</span>
                            @error('form.codigo') <span class="nx-field-error">{{ $message }}</span> @enderror
                            @error('codigo') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Tipo de Movimento <span class="nx-required">*</span></label>
                            <select wire:model.live="form.tipo_movimento">
                                @foreach($movimentos as $mov)
                                    <option value="{{ $mov->value }}">{{ $mov->icon() }} {{ $mov->label() }}</option>
                                @endforeach
                            </select>
                            @error('form.tipo_movimento') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Descrição <span class="nx-required">*</span></label>
                        <input type="text" wire:model.blur="form.descricao" placeholder="Ex: Venda de Mercadoria Estadual" maxlength="255">
                        @error('form.descricao') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Natureza da Operação</label>
                        <input type="text" wire:model.blur="form.natureza_operacao" placeholder="Ex: Venda de Mercadoria" maxlength="100">
                        <span style="font-size:11px;color:#94A3B8;">Texto que aparecerá no campo "Natureza da Operação" da NF-e</span>
                        @error('form.natureza_operacao') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Observações</label>
                        <textarea wire:model.blur="form.observacoes" rows="3" placeholder="Notas internas sobre esta operação..."></textarea>
                        @error('form.observacoes') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-field">
                        <label>Status</label>
                        <label class="nx-toggle-row" style="padding:9px 12px;border:1px solid #E2E8F0;border-radius:8px;cursor:pointer;margin:0;max-width:280px;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label" style="font-size:14px;">{{ $form->is_active ? 'Operação Ativa' : 'Operação Inativa' }}</span>
                            </div>
                            <span class="nx-switch">
                                <input type="checkbox" wire:model.live="form.is_active">
                                <span class="nx-switch-track"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: CFOP                                             --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'cfop'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">CFOP – Código Fiscal de Operações</h3>
                    </div>

                    {{-- Info card sobre CFOP --}}
                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:16px;margin-bottom:20px;">
                        <div style="font-size:13px;font-weight:600;color:#1D4ED8;margin-bottom:8px;">📌 Guia Rápido de CFOP</div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;font-size:12px;color:#1E40AF;">
                            <div><strong>1xxx</strong> – Entradas Estaduais</div>
                            <div><strong>2xxx</strong> – Entradas Interestaduais</div>
                            <div><strong>3xxx</strong> – Entradas do Exterior</div>
                            <div><strong>5xxx</strong> – Saídas Estaduais</div>
                            <div><strong>6xxx</strong> – Saídas Interestaduais</div>
                            <div><strong>7xxx</strong> – Saídas para o Exterior</div>
                        </div>
                        <div style="margin-top:10px;font-size:11px;color:#3B82F6;">CFOPs comuns: 5102 (Venda Estadual), 6102 (Venda Interestadual), 1202 (Dev. Venda Estadual), 5202 (Dev. Compra Estadual)</div>
                    </div>

                    <div class="nx-field" style="max-width:300px;">
                        <label>Código CFOP</label>
                        <input type="text" wire:model.blur="form.cfop" placeholder="Ex: 5102" maxlength="4" pattern="[1-7][0-9]{3}"
                            style="font-family:monospace;font-size:18px;font-weight:700;letter-spacing:4px;text-align:center;">
                        <span style="font-size:11px;color:#94A3B8;">4 dígitos numéricos</span>
                        @error('form.cfop') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    @if($form->cfop && strlen($form->cfop) === 4)
                        @php
                            $prefix = substr($form->cfop, 0, 1);
                            $cfopLabel = match($prefix) {
                                '1' => ['Entrada Estadual', '#10B981', '#D1FAE5'],
                                '2' => ['Entrada Interestadual', '#3B82F6', '#DBEAFE'],
                                '3' => ['Entrada do Exterior', '#8B5CF6', '#EDE9FE'],
                                '5' => ['Saída Estadual', '#F59E0B', '#FEF3C7'],
                                '6' => ['Saída Interestadual', '#EF4444', '#FEE2E2'],
                                '7' => ['Saída para Exterior', '#EC4899', '#FCE7F3'],
                                default => ['Desconhecido', '#6B7280', '#F3F4F6'],
                            };
                        @endphp
                        <div style="margin-top:12px;display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:{{ $cfopLabel[2] }};border-radius:8px;border:1px solid {{ $cfopLabel[1] }}20;">
                            <span style="font-family:monospace;font-size:16px;font-weight:700;color:{{ $cfopLabel[1] }};">{{ $form->cfop }}</span>
                            <span style="font-size:13px;color:{{ $cfopLabel[1] }};font-weight:500;">{{ $cfopLabel[0] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: ICMS                                             --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'icms'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">ICMS – Imposto sobre Circulação de Mercadorias</h3>
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>CST / CSOSN do ICMS</label>
                        <select wire:model.live="form.icms_cst">
                            <option value="">— Selecione —</option>
                            <optgroup label="── Regime Normal (CST) ──">
                                @foreach(['00','10','20','30','40','41','50','51','60','70','90'] as $cst)
                                    <option value="{{ $cst }}" @selected($form->icms_cst === $cst)>{{ $cstIcms[$cst] ?? $cst }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="── Simples Nacional (CSOSN) ──">
                                @foreach(['101','102','103','201','202','203','300','400','500','900'] as $csosn)
                                    <option value="{{ $csosn }}" @selected($form->icms_cst === $csosn)>{{ $cstIcms[$csosn] ?? $csosn }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('form.icms_cst') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-2" style="margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Modalidade de Determinação da BC</label>
                            <select wire:model.live="form.icms_modalidade_bc">
                                <option value="">— Selecione —</option>
                                @foreach($icmsModalidades as $mod)
                                    <option value="{{ $mod->value }}" @selected($form->icms_modalidade_bc == $mod->value)>{{ $mod->label() }}</option>
                                @endforeach
                            </select>
                            @error('form.icms_modalidade_bc') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Alíquota ICMS (%)</label>
                            <input type="number" wire:model.blur="form.icms_aliquota" placeholder="0.00" min="0" max="100" step="0.01">
                            @error('form.icms_aliquota') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="nx-field" style="max-width:280px;">
                        <label>Redução da Base de Cálculo (%)</label>
                        <input type="number" wire:model.blur="form.icms_reducao_bc" placeholder="0.00" min="0" max="100" step="0.01">
                        <span style="font-size:11px;color:#94A3B8;">Preencha quando a BC for reduzida (CST 20, 70)</span>
                        @error('form.icms_reducao_bc') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: IPI                                              --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'ipi'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">IPI – Imposto sobre Produtos Industrializados</h3>
                    </div>

                    <div class="grid grid-2" style="margin-bottom:16px;">
                        <div class="nx-field">
                            <label>CST do IPI</label>
                            <select wire:model.live="form.ipi_cst">
                                <option value="">— Selecione —</option>
                                <optgroup label="── Entradas ──">
                                    @foreach(['00','01','02','03','04','05','49'] as $cst)
                                        <option value="{{ $cst }}" @selected($form->ipi_cst === $cst)>{{ $cstIpi[$cst] ?? $cst }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="── Saídas ──">
                                    @foreach(['50','51','52','53','54','55','99'] as $cst)
                                        <option value="{{ $cst }}" @selected($form->ipi_cst === $cst)>{{ $cstIpi[$cst] ?? $cst }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('form.ipi_cst') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Modalidade de Cálculo do IPI</label>
                            <select wire:model.live="form.ipi_modalidade">
                                <option value="">— Selecione —</option>
                                @foreach($ipiModalidades as $mod)
                                    <option value="{{ $mod->value }}" @selected($form->ipi_modalidade === $mod->value)>{{ $mod->label() }}</option>
                                @endforeach
                            </select>
                            @error('form.ipi_modalidade') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="nx-field" style="max-width:280px;">
                        <label>Alíquota IPI (%)</label>
                        <input type="number" wire:model.blur="form.ipi_aliquota" placeholder="0.00" min="0" max="100" step="0.01">
                        @error('form.ipi_aliquota') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: PIS / COFINS                                     --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'pis-cofins'])>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                {{-- PIS --}}
                <div class="nx-form-card">
                    <div class="nx-form-section" style="border-bottom:none;">
                        <div class="nx-form-section-header">
                            <div class="nx-form-section-icon" style="background:#EDE9FE;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <h3 class="nx-form-section-title" style="font-size:15px;">PIS</h3>
                        </div>
                        <div class="nx-field" style="margin-bottom:14px;">
                            <label>CST do PIS</label>
                            <select wire:model.live="form.pis_cst">
                                <option value="">— Selecione —</option>
                                @foreach($cstPisCofins as $code => $label)
                                    <option value="{{ $code }}" @selected($form->pis_cst === (string)$code)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.pis_cst') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Alíquota PIS (%)</label>
                            <input type="number" wire:model.blur="form.pis_aliquota" placeholder="0.65" min="0" max="100" step="0.01">
                            @error('form.pis_aliquota') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- COFINS --}}
                <div class="nx-form-card">
                    <div class="nx-form-section" style="border-bottom:none;">
                        <div class="nx-form-section-header">
                            <div class="nx-form-section-icon" style="background:#FCE7F3;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <h3 class="nx-form-section-title" style="font-size:15px;">COFINS</h3>
                        </div>
                        <div class="nx-field" style="margin-bottom:14px;">
                            <label>CST do COFINS</label>
                            <select wire:model.live="form.cofins_cst">
                                <option value="">— Selecione —</option>
                                @foreach($cstPisCofins as $code => $label)
                                    <option value="{{ $code }}" @selected($form->cofins_cst === (string)$code)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.cofins_cst') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Alíquota COFINS (%)</label>
                            <input type="number" wire:model.blur="form.cofins_aliquota" placeholder="3.00" min="0" max="100" step="0.01">
                            @error('form.cofins_aliquota') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Botões de ação ──────────────────────────────── --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
            <a href="{{ route('fiscal.tipo-operacao.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Criar Tipo de Operação' }}
            </button>
        </div>

    </form>
</div>

