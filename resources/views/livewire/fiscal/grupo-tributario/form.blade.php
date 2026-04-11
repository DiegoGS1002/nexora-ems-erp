<div class="nx-list-page">

    {{-- Cabeçalho --}}
    <div class="nx-form-header" style="margin-bottom:24px;">
        <a href="{{ route('fiscal.grupo-tributario.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Grupos Tributários
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Grupo Tributário' : 'Novo Grupo Tributário' }}</h1>
                <p class="nx-form-subtitle">Configure NCM, regime tributário, operações fiscais e tributos padrão para os produtos</p>
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
            <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form wire:submit="save">

        {{-- ── Abas de navegação ────────────────────────── --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key' => 'geral',      'label' => 'Identificação',     'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                    ['key' => 'operacoes',  'label' => 'Operações Fiscais', 'icon' => '<polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>'],
                    ['key' => 'icms',       'label' => 'ICMS',              'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
                    ['key' => 'ipi',        'label' => 'IPI',               'icon' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
                    ['key' => 'pis-cofins', 'label' => 'PIS / COFINS',      'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
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
        {{-- ABA: IDENTIFICAÇÃO                                     --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'geral'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">Identificação do Grupo</h3>
                        <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                    </div>

                    <div class="grid grid-2" style="gap:16px;margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Código <span class="nx-required">*</span></label>
                            <input type="text" wire:model.blur="form.codigo" placeholder="Ex: GT-MERCH-SN" maxlength="20" style="text-transform:uppercase;">
                            <span style="font-size:11px;color:#94A3B8;">Identificador único (máx. 20 car.)</span>
                            @error('form.codigo') <span class="nx-field-error">{{ $message }}</span> @enderror
                            @error('codigo') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Regime Tributário <span class="nx-required">*</span></label>
                            <select wire:model.live="form.regime_tributario">
                                @foreach($regimes as $r)
                                    <option value="{{ $r->value }}">{{ $r->label() }}</option>
                                @endforeach
                            </select>
                            @error('form.regime_tributario') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Nome do Grupo <span class="nx-required">*</span></label>
                        <input type="text" wire:model.blur="form.nome" placeholder="Ex: Mercadoria para Revenda – Simples Nacional" maxlength="150">
                        @error('form.nome') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Descrição / Observações</label>
                        <textarea wire:model.blur="form.descricao" rows="3" placeholder="Descreva quando usar este grupo tributário..."></textarea>
                        @error('form.descricao') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-2" style="gap:16px;margin-bottom:16px;">
                        <div class="nx-field">
                            <label>NCM Padrão</label>
                            {{-- NCM info box --}}
                            <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:10px 12px;margin-bottom:8px;font-size:12px;color:#166534;">
                                <div style="font-weight:600;margin-bottom:4px;">📦 Nomenclatura Comum do Mercosul</div>
                                <div>8 dígitos numéricos que classificam a mercadoria. Ex: <span style="font-family:monospace;font-weight:700;">6403.99.00</span></div>
                            </div>
                            <input type="text" wire:model.blur="form.ncm"
                                placeholder="00000000" maxlength="8"
                                style="font-family:monospace;font-size:15px;font-weight:600;letter-spacing:2px;text-align:center;">
                            @if($form->ncm && strlen(preg_replace('/\D/', '', $form->ncm)) === 8)
                                @php $ncm = preg_replace('/\D/', '', $form->ncm); @endphp
                                <div style="margin-top:6px;display:inline-flex;align-items:center;gap:8px;padding:6px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;">
                                    <span style="font-family:monospace;font-size:15px;font-weight:700;color:#15803D;">
                                        {{ substr($ncm,0,4) }}.{{ substr($ncm,4,2) }}.{{ substr($ncm,6,2) }}
                                    </span>
                                    <span style="font-size:12px;color:#16A34A;">NCM válido</span>
                                </div>
                            @endif
                            @error('form.ncm') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Status</label>
                            <label class="nx-toggle-row" style="padding:9px 12px;border:1px solid #E2E8F0;border-radius:8px;cursor:pointer;margin:0;max-width:280px;">
                                <div class="nx-toggle-info">
                                    <span class="nx-toggle-label" style="font-size:14px;">{{ $form->is_active ? 'Grupo Ativo' : 'Grupo Inativo' }}</span>
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
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- ABA: OPERAÇÕES FISCAIS                                 --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'operacoes'])>
            <div class="nx-form-card">
                <div class="nx-form-section" style="border-bottom:none;">
                    <div class="nx-form-section-header">
                        <div class="nx-form-section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">Tipos de Operação Vinculados</h3>
                    </div>

                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12px;color:#1E40AF;">
                        <div style="font-size:13px;font-weight:600;color:#1D4ED8;margin-bottom:6px;">📌 Para que serve este vínculo?</div>
                        Ao vincular <strong>Tipos de Operação Fiscal</strong>, o sistema preencherá automaticamente o CFOP e o CST ao usar este grupo em pedidos de venda ou compra, conforme a direção (saída ou entrada).
                    </div>

                    <div class="grid grid-2" style="gap:16px;">
                        <div class="nx-field">
                            <label>
                                <span style="display:inline-flex;align-items:center;gap:6px;">
                                    <span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;display:inline-block;flex-shrink:0;"></span>
                                    Tipo de Operação — <strong>Saída</strong>
                                </span>
                            </label>
                            <select wire:model.live="form.tipo_operacao_saida_id">
                                <option value="">— Nenhuma —</option>
                                @foreach($tiposOperacaoSaida as $top)
                                    <option value="{{ $top->id }}">{{ $top->codigo }} – {{ $top->descricao }} @if($top->cfop)(CFOP {{ $top->cfop }})@endif</option>
                                @endforeach
                            </select>
                            @error('form.tipo_operacao_saida_id') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>
                                <span style="display:inline-flex;align-items:center;gap:6px;">
                                    <span style="width:10px;height:10px;border-radius:50%;background:#3B82F6;display:inline-block;flex-shrink:0;"></span>
                                    Tipo de Operação — <strong>Entrada</strong>
                                </span>
                            </label>
                            <select wire:model.live="form.tipo_operacao_entrada_id">
                                <option value="">— Nenhuma —</option>
                                @foreach($tiposOperacaoEntrada as $top)
                                    <option value="{{ $top->id }}">{{ $top->codigo }} – {{ $top->descricao }} @if($top->cfop)(CFOP {{ $top->cfop }})@endif</option>
                                @endforeach
                            </select>
                            @error('form.tipo_operacao_entrada_id') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($form->tipo_operacao_saida_id || $form->tipo_operacao_entrada_id)
                    <div style="margin-top:16px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;padding:14px 16px;">
                        <div style="font-size:11px;font-weight:600;color:#64748B;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.05em;">Resumo das Operações Selecionadas</div>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;">
                            @if($form->tipo_operacao_saida_id)
                                @php $opS = $tiposOperacaoSaida->find($form->tipo_operacao_saida_id); @endphp
                                @if($opS)
                                <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:8px;padding:10px 14px;flex:1;min-width:180px;">
                                    <div style="font-size:10px;font-weight:700;color:#92400E;margin-bottom:4px;letter-spacing:0.05em;">↑ SAÍDA</div>
                                    <div style="font-family:monospace;font-size:16px;font-weight:700;color:#B45309;">CFOP {{ $opS->cfop ?? '—' }}</div>
                                    <div style="font-size:12px;color:#78350F;margin-top:2px;">{{ $opS->descricao }}</div>
                                </div>
                                @endif
                            @endif
                            @if($form->tipo_operacao_entrada_id)
                                @php $opE = $tiposOperacaoEntrada->find($form->tipo_operacao_entrada_id); @endphp
                                @if($opE)
                                <div style="background:#DBEAFE;border:1px solid #BFDBFE;border-radius:8px;padding:10px 14px;flex:1;min-width:180px;">
                                    <div style="font-size:10px;font-weight:700;color:#1E3A5F;margin-bottom:4px;letter-spacing:0.05em;">↓ ENTRADA</div>
                                    <div style="font-family:monospace;font-size:16px;font-weight:700;color:#1D4ED8;">CFOP {{ $opE->cfop ?? '—' }}</div>
                                    <div style="font-size:12px;color:#1E40AF;margin-top:2px;">{{ $opE->descricao }}</div>
                                </div>
                                @endif
                            @endif
                        </div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h3 class="nx-form-section-title">ICMS – Imposto sobre Circulação de Mercadorias</h3>
                    </div>

                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12px;color:#1E40AF;">
                        <div style="font-size:13px;font-weight:600;color:#1D4ED8;margin-bottom:6px;">📌 Dica de preenchimento</div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:6px;">
                            <div><strong>Simples Nacional</strong> → Use CSOSN (códigos 1xx, 2xx, 3xx, 4xx, 5xx, 9xx)</div>
                            <div><strong>Regime Normal</strong> → Use CST (códigos 00 a 90)</div>
                            <div><strong>CST 20 / 70</strong> → Preencha a Redução da BC</div>
                        </div>
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

                    <div class="grid grid-2" style="gap:16px;margin-bottom:16px;">
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

                    <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12px;color:#92400E;">
                        <div style="font-size:13px;font-weight:600;color:#B45309;margin-bottom:6px;">📌 Quando preencher o IPI?</div>
                        O IPI se aplica a <strong>produtos industrializados</strong>. Empresas comerciais geralmente não recolhem IPI, mas precisam destacá-lo na NF-e para bens de capital e revendas específicas.
                    </div>

                    <div class="grid grid-2" style="gap:16px;margin-bottom:16px;">
                        <div class="nx-field">
                            <label>CST do IPI</label>
                            <select wire:model.live="form.ipi_cst">
                                <option value="">— Selecione —</option>
                                <optgroup label="── Entradas ──">
                                    @foreach(['00','49'] as $cst)
                                        <option value="{{ $cst }}" @selected($form->ipi_cst === $cst)>{{ $cstIpi[$cst] ?? $cst }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="── Saídas ──">
                                    @foreach(['50','52','53','54','55','99'] as $cst)
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

            <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:10px;padding:14px 16px;margin-bottom:16px;font-size:12px;color:#5B21B6;">
                <div style="font-size:13px;font-weight:600;color:#7C3AED;margin-bottom:6px;">📌 PIS e COFINS – contribuições federais</div>
                Empresas do <strong>Lucro Presumido / Lucro Real</strong> utilizam o regime cumulativo (alíq. PIS 0,65% / COFINS 3%) ou não-cumulativo (1,65% / 7,6%). Simples Nacional calcula dentro do DAS.
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                {{-- PIS --}}
                <div class="nx-form-card">
                    <div class="nx-form-section" style="border-bottom:none;">
                        <div class="nx-form-section-header">
                            <div class="nx-form-section-icon" style="background:#EDE9FE;border-color:#DDD6FE;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <h3 class="nx-form-section-title" style="font-size:15px;">PIS</h3>
                        </div>
                        <div class="nx-field" style="margin-bottom:14px;">
                            <label>CST do PIS</label>
                            <select wire:model.live="form.pis_cst">
                                <option value="">— Selecione —</option>
                                @foreach($cstPisCofins as $code => $lbl)
                                    <option value="{{ $code }}" @selected($form->pis_cst === (string)$code)>{{ $lbl }}</option>
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
                            <div class="nx-form-section-icon" style="background:#FCE7F3;border-color:#FBCFE8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <h3 class="nx-form-section-title" style="font-size:15px;">COFINS</h3>
                        </div>
                        <div class="nx-field" style="margin-bottom:14px;">
                            <label>CST do COFINS</label>
                            <select wire:model.live="form.cofins_cst">
                                <option value="">— Selecione —</option>
                                @foreach($cstPisCofins as $code => $lbl)
                                    <option value="{{ $code }}" @selected($form->cofins_cst === (string)$code)>{{ $lbl }}</option>
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
            <a href="{{ route('fiscal.grupo-tributario.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Criar Grupo Tributário' }}
            </button>
        </div>

    </form>
</div>

