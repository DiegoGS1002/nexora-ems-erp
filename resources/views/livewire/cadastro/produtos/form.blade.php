<div class="nx-product-page">

    {{-- ═══════ HEADER ═══════ --}}
    <div class="nx-form-header" style="max-width:1200px;margin:0 auto 24px;">
        <a href="{{ route('products.index') }}" class="nx-back-link" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Produtos
        </a>
        <div class="nx-form-header-row">
            <div>
                <h1 class="nx-form-title">{{ $isEditing ? 'Editar Produto' : 'Novo Produto' }}</h1>
                <p class="nx-form-subtitle">{{ $isEditing ? 'Atualize os dados do cadastro' : 'Preencha as informações para cadastrar o produto no catálogo' }}</p>
            </div>
            @if($isEditing)
                <span class="nx-status-badge nx-status-badge--edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editando
                </span>
            @endif
        </div>
    </div>

    {{-- ═══════ FLASH ═══════ --}}
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

    <form wire:submit="save" enctype="multipart/form-data" style="max-width:1200px;margin:0 auto;">

        {{-- ═══════ TABS ═══════ --}}
        <div class="nx-product-tabs">
            @php
                $tabs = [
                    ['key'=>'dados-gerais',       'label'=>'Dados Gerais',        'icon'=>'<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1" ry="1"/>'],
                    ['key'=>'precos-custos',       'label'=>'Preços e Custos',     'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                    ['key'=>'estoque',             'label'=>'Estoque',             'icon'=>'<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
                    ['key'=>'imagens',             'label'=>'Imagens e Mídia',     'icon'=>'<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>'],
                    ['key'=>'fornecedores',        'label'=>'Fornecedores',        'icon'=>'<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
                    ['key'=>'tributacao',          'label'=>'Tributação',          'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                    ['key'=>'detalhes',            'label'=>'Detalhes Adicionais', 'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
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

        {{-- ═══════ CONTEÚDO (2/3 + 1/3) ═══════ --}}
        <div class="nx-product-layout">

            {{-- ─── COLUNA PRINCIPAL ─── --}}
            <div class="nx-product-main">

                {{-- ══ ABA: DADOS GERAIS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'dados-gerais'])>

                    {{-- INFORMAÇÕES BÁSICAS --}}
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Informações Básicas</h3>
                                <span class="nx-section-hint">Campos com <span style="color:#EF4444">*</span> são obrigatórios</span>
                            </div>

                            {{-- Código + EAN --}}
                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Código do Produto</label>
                                    <div style="position:relative;">
                                        <input type="text" value="{{ $product?->product_code ?? 'Gerado automaticamente' }}" readonly
                                            style="background:#F8FAFC;cursor:not-allowed;padding-right:36px;color:#94A3B8;font-family:'JetBrains Mono','Courier New',monospace;font-size:12.5px;">
                                        <svg style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:#CBD5E1;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </div>
                                </div>
                                <div class="nx-field">
                                    <label>Código de Barras (EAN)</label>
                                    <input type="text" wire:model.blur="form.ean" placeholder="7891234567890" maxlength="13">
                                    @error('form.ean') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Nome + Descrição Curta --}}
                            <div class="nx-field" style="margin-bottom:16px;">
                                <label>Nome do Produto <span class="nx-required">*</span></label>
                                <input type="text" wire:model.blur="form.name" placeholder="Ex: Monitor LED 24&quot; Full HD" maxlength="120">
                                @error('form.name') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="nx-field" style="margin-bottom:16px;">
                                <label>Descrição Curta</label>
                                <input type="text" wire:model.blur="form.short_description" placeholder="Breve resumo comercial do produto (até 200 caracteres)" maxlength="200">
                                @error('form.short_description') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Categoria + Marca --}}
                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Categoria <span class="nx-required">*</span></label>
                                    <div style="display:flex;gap:8px;">
                                        <select wire:model.blur="form.category" style="flex:1;">
                                            <option value="">Selecione a categoria</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="openCategoryModal" class="nx-btn-icon" title="Criar nova categoria">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    </div>
                                    @error('form.category') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Marca / Fabricante</label>
                                    <input type="text" wire:model.blur="form.brand" placeholder="Ex: Dell, Samsung, Bosch">
                                    @error('form.brand') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Unidade + Linha --}}
                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Unidade de Medida <span class="nx-required">*</span></label>
                                    <div style="display:flex;gap:8px;">
                                        <select wire:model.blur="form.unit_of_measure" style="flex:1;">
                                            <option value="">Selecione a unidade</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->abbreviation }} — {{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="openUnitModal" class="nx-btn-icon" title="Criar nova unidade">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    </div>
                                    @error('form.unit_of_measure') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Linha de Produto</label>
                                    <input type="text" wire:model.blur="form.product_line" placeholder="Ex: Monitores, Linha Premium">
                                    @error('form.product_line') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Tipo + Natureza --}}
                            <div style="margin-bottom:8px;">
                                <label style="margin-bottom:10px;">Tipo de Produto <span class="nx-required">*</span></label>
                                <div class="nx-tipo-selector">
                                    @foreach($tipoProdutos as $tipo)
                                        <label class="nx-tipo-card {{ $form->product_type === $tipo->value ? 'nx-tipo-card--active' : '' }}">
                                            <input type="radio" wire:model.live="form.product_type" value="{{ $tipo->value }}" style="display:none;">
                                            <div class="nx-tipo-card-icon">
                                                @if($tipo->value === 'produto_fisico')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                @endif
                                            </div>
                                            <div class="nx-tipo-card-text">
                                                <span class="nx-tipo-card-label">{{ $tipo->label() }}</span>
                                                <span class="nx-tipo-card-sub">{{ $tipo->value === 'produto_fisico' ? 'Item físico com estoque' : 'Prestação de serviço' }}</span>
                                            </div>
                                            <div class="nx-tipo-card-check {{ $form->product_type === $tipo->value ? 'nx-tipo-card-check--active' : '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="nx-field" style="margin-top:16px;">
                                <label>Natureza <span class="nx-required">*</span></label>
                                <select wire:model.blur="form.nature">
                                    @foreach($naturezas as $nat)
                                        <option value="{{ $nat->value }}">{{ $nat->label() }}</option>
                                    @endforeach
                                </select>
                                @error('form.nature') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- DIMENSÕES E PESO --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Dimensões e Peso</h3>
                                <span class="nx-section-hint">Para cálculo de frete e logística</span>
                            </div>

                            <div class="grid grid-2" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>Peso Líquido (kg)</label>
                                    <div style="position:relative;">
                                        <input type="number" step="0.001" wire:model.blur="form.weight_net" placeholder="0,000" style="padding-right:42px;">
                                        <span class="nx-input-unit">kg</span>
                                    </div>
                                    @error('form.weight_net') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Peso Bruto (kg)</label>
                                    <div style="position:relative;">
                                        <input type="number" step="0.001" wire:model.blur="form.weight_gross" placeholder="0,000" style="padding-right:42px;">
                                        <span class="nx-input-unit">kg</span>
                                    </div>
                                    @error('form.weight_gross') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-3">
                                <div class="nx-field">
                                    <label>Altura (cm)</label>
                                    <div style="position:relative;">
                                        <input type="number" step="0.01" wire:model.blur="form.height" placeholder="0,00" style="padding-right:42px;">
                                        <span class="nx-input-unit">cm</span>
                                    </div>
                                    @error('form.height') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Largura (cm)</label>
                                    <div style="position:relative;">
                                        <input type="number" step="0.01" wire:model.blur="form.width" placeholder="0,00" style="padding-right:42px;">
                                        <span class="nx-input-unit">cm</span>
                                    </div>
                                    @error('form.width') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Profundidade (cm)</label>
                                    <div style="position:relative;">
                                        <input type="number" step="0.01" wire:model.blur="form.depth" placeholder="0,00" style="padding-right:42px;">
                                        <span class="nx-input-unit">cm</span>
                                    </div>
                                    @error('form.depth') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIÇÃO COMPLETA --}}
                    <div class="nx-form-card" style="margin-top:16px;">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Descrição Completa</h3>
                                <span class="nx-section-hint">Especificações técnicas e detalhes comerciais</span>
                            </div>
                            <div class="nx-field" style="margin-bottom:12px;">
                                <label>Descrição Básica</label>
                                <textarea wire:model.blur="form.description" rows="3" placeholder="Descrição resumida do produto..."></textarea>
                                @error('form.description') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="nx-field">
                                <label>Descrição Detalhada</label>
                                <textarea wire:model.blur="form.full_description" rows="6" placeholder="Especificações técnicas completas, diferenciais, tabela de medidas..."></textarea>
                                @error('form.full_description') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: PREÇOS E CUSTOS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'precos-custos'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Preços e Custos</h3>
                            </div>
                            <div class="grid grid-2">
                                <div class="nx-field">
                                    <label>Preço de Venda (R$)</label>
                                    <div style="position:relative;">
                                        <span class="nx-input-prefix">R$</span>
                                        <input type="number" step="0.01" wire:model.blur="form.sale_price" placeholder="0,00" style="padding-left:42px;">
                                    </div>
                                    @error('form.sale_price') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Preço de Custo (R$)</label>
                                    <div style="position:relative;">
                                        <span class="nx-input-prefix">R$</span>
                                        <input type="number" step="0.01" wire:model.blur="form.cost_price" placeholder="0,00" style="padding-left:42px;">
                                    </div>
                                    @error('form.cost_price') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($form->sale_price && $form->cost_price && floatval($form->cost_price) > 0)
                                @php
                                    $margem = (( floatval($form->sale_price) - floatval($form->cost_price)) / floatval($form->cost_price)) * 100;
                                @endphp
                                <div class="nx-inline-alert {{ $margem >= 20 ? 'nx-inline-alert--success' : 'nx-inline-alert--warn' }}" style="margin-top:16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <span>Margem de lucro estimada: <strong>{{ number_format($margem, 1) }}%</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: ESTOQUE ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'estoque'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Controle de Estoque</h3>
                            </div>
                            <div class="grid grid-3">
                                <div class="nx-field">
                                    <label>Estoque Atual</label>
                                    <input type="number" wire:model.blur="form.stock" placeholder="0">
                                    @error('form.stock') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Estoque Mínimo</label>
                                    <input type="number" wire:model.blur="form.stock_min" placeholder="0">
                                    <small>Alerta quando atingir este nível</small>
                                    @error('form.stock_min') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>Data de Validade</label>
                                    <input type="date" wire:model.blur="form.expiration_date">
                                    <small>Deixe em branco se não aplicável</small>
                                    @error('form.expiration_date') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: IMAGENS E MÍDIA ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'imagens'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Imagem Principal</h3>
                                <span class="nx-section-hint">PNG, JPG, WEBP — máx. 5MB</span>
                            </div>

                            <div class="nx-dropzone" x-data="{dragover:false}" @dragover.prevent="dragover=true" @dragleave="dragover=false" @drop.prevent="dragover=false" :class="dragover && 'nx-dropzone--over'">
                                @if($image)
                                    <div class="nx-dropzone-preview">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview">
                                        <button type="button" wire:click="$set('image', null)" class="nx-dropzone-remove" title="Remover">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                @elseif($isEditing && $product?->image)
                                    <div class="nx-dropzone-preview">
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                    </div>
                                @else
                                    <div class="nx-dropzone-placeholder">
                                        <div class="nx-dropzone-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                        <p class="nx-dropzone-label">Arraste uma imagem ou <strong>clique para selecionar</strong></p>
                                        <p class="nx-dropzone-hint">PNG, JPG, WEBP — até 5MB</p>
                                    </div>
                                @endif
                                <input type="file" wire:model="image" accept="image/png,image/jpeg,image/webp" class="nx-dropzone-input">
                            </div>
                            @error('image') <span class="nx-field-error" style="margin-top:8px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: FORNECEDORES ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'fornecedores'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Fornecedores Associados</h3>
                            </div>
                            <div class="nx-supplier-grid">
                                @forelse($suppliers as $supplier)
                                    <label class="nx-supplier-option {{ in_array($supplier->id, $supplier_ids) ? 'nx-supplier-option--active' : '' }}">
                                        <input type="checkbox" wire:model.live="supplier_ids" value="{{ $supplier->id }}" style="display:none;">
                                        <div class="nx-supplier-avatar">{{ strtoupper(substr($supplier->social_name ?? $supplier->name ?? 'S', 0, 2)) }}</div>
                                        <div class="nx-supplier-info">
                                            <span class="nx-supplier-name">{{ $supplier->social_name ?? $supplier->name }}</span>
                                            @if($supplier->taxNumber ?? false)
                                                <span class="nx-supplier-doc">{{ $supplier->taxNumber }}</span>
                                            @endif
                                        </div>
                                        <div class="nx-supplier-check {{ in_array($supplier->id, $supplier_ids) ? 'nx-supplier-check--active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    </label>
                                @empty
                                    <div class="nx-inline-alert nx-inline-alert--warn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        <span>Nenhum fornecedor cadastrado. <a href="{{ route('suppliers.create') }}" wire:navigate style="color:inherit;font-weight:700;">Cadastrar fornecedor</a></span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: TRIBUTAÇÃO ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'tributacao'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Tributação Fiscal</h3>
                            </div>

                            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px;margin-bottom:20px;font-size:12px;color:#1E40AF;">
                                💡 Vincule um <strong>Grupo Tributário</strong> para pré-preencher NCM, CFOP e CST automaticamente. Ou configure manualmente os campos abaixo.
                            </div>

                            <div class="nx-field" style="margin-bottom:20px;">
                                <label>Grupo Tributário</label>
                                <select wire:model.live="form.grupo_tributario_id">
                                    <option value="">— Nenhum —</option>
                                    @foreach($gruposTributarios as $gt)
                                        <option value="{{ $gt->id }}">[{{ $gt->codigo }}] {{ $gt->nome }} @if($gt->ncm)– NCM {{ $gt->ncm }}@endif</option>
                                    @endforeach
                                </select>
                                <span style="font-size:11px;color:#94A3B8;">Ao selecionar um grupo, os campos abaixo serão preenchidos automaticamente</span>
                                @error('form.grupo_tributario_id') <span class="nx-field-error">{{ $message }}</span> @enderror
                            </div>

                            @if($form->grupo_tributario_id)
                                @php $grupoSel = $gruposTributarios->find($form->grupo_tributario_id); @endphp
                                @if($grupoSel)
                                <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                                    <div style="font-size:12px;font-weight:600;color:#15803D;margin-bottom:6px;">✓ Grupo Tributário Aplicado</div>
                                    <div style="display:flex;gap:12px;flex-wrap:wrap;font-size:11px;color:#166534;">
                                        <span><strong>Código:</strong> {{ $grupoSel->codigo }}</span>
                                        @if($grupoSel->ncm)
                                            <span><strong>NCM:</strong> {{ $grupoSel->ncm }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endif

                            <div class="grid grid-3" style="margin-bottom:16px;">
                                <div class="nx-field">
                                    <label>NCM</label>
                                    <input type="text" wire:model.blur="form.ncm" placeholder="00000000" maxlength="8"
                                        style="font-family:monospace;font-size:14px;font-weight:700;letter-spacing:2px;text-align:center;">
                                    <span style="font-size:11px;color:#94A3B8;">8 dígitos – Nomenclatura Comum do Mercosul</span>
                                    @if($form->ncm && strlen(preg_replace('/\D/', '', $form->ncm)) === 8)
                                        @php $ncmDigits = preg_replace('/\D/', '', $form->ncm); @endphp
                                        <div style="margin-top:4px;font-family:monospace;font-size:12px;font-weight:700;color:#1D4ED8;">
                                            {{ substr($ncmDigits,0,4) }}.{{ substr($ncmDigits,4,2) }}.{{ substr($ncmDigits,6,2) }}
                                        </div>
                                    @endif
                                    @error('form.ncm') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>CFOP Saída</label>
                                    <input type="text" wire:model.blur="form.cfop_saida" placeholder="5102" maxlength="4"
                                        style="font-family:monospace;font-size:14px;font-weight:700;letter-spacing:3px;text-align:center;">
                                    <span style="font-size:11px;color:#94A3B8;">CFOP para vendas / saídas</span>
                                    @error('form.cfop_saida') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="nx-field">
                                    <label>CFOP Entrada</label>
                                    <input type="text" wire:model.blur="form.cfop_entrada" placeholder="1102" maxlength="4"
                                        style="font-family:monospace;font-size:14px;font-weight:700;letter-spacing:3px;text-align:center;">
                                    <span style="font-size:11px;color:#94A3B8;">CFOP para compras / entradas</span>
                                    @error('form.cfop_entrada') <span class="nx-field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div style="background:#FEF9C3;border:1px solid #FDE047;border-radius:10px;padding:12px 14px;margin-top:16px;font-size:11px;color:#854D0E;">
                                <strong>💡 Dica:</strong> Para configurações avançadas de ICMS, IPI, PIS e COFINS, edite o <strong>Grupo Tributário</strong> ou vá em <strong>Fiscal → Grupos Tributários</strong>.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ ABA: DETALHES ADICIONAIS ══ --}}
                <div @class(['nx-tab-panel', 'nx-tab-panel--active' => $activeTab === 'detalhes'])>
                    <div class="nx-form-card">
                        <div class="nx-form-section" style="border-bottom:none;">
                            <div class="nx-form-section-header">
                                <div class="nx-form-section-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </div>
                                <h3 class="nx-form-section-title">Detalhes Adicionais</h3>
                            </div>
                            <div class="nx-inline-alert nx-inline-alert--warn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Campos adicionais como garantia, ficha técnica e certificações serão adicionados em breve.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /nx-product-main --}}

            {{-- ─── SIDEBAR ─── --}}
            <aside class="nx-product-sidebar">

                {{-- STATUS --}}
                <div class="nx-sidebar-card">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Status do Produto
                    </div>
                    <div class="nx-sidebar-card-body">
                        <label class="nx-toggle-row" style="padding:0;border:none;cursor:pointer;">
                            <div class="nx-toggle-info">
                                <span class="nx-toggle-label">{{ $form->is_active ? 'Ativo' : 'Inativo' }}</span>
                                <p class="nx-toggle-desc">{{ $form->is_active ? 'Produto visível em vendas e buscas' : 'Produto oculto das buscas e vendas' }}</p>
                            </div>
                            <span class="nx-switch">
                                <input type="checkbox" wire:model.live="form.is_active">
                                <span class="nx-switch-track"></span>
                            </span>
                        </label>
                        @if($isEditing && $product?->created_at)
                            <div style="margin-top:14px;padding-top:14px;border-top:1px solid #F1F5F9;">
                                <p style="font-size:11.5px;color:#94A3B8;margin:0 0 2px;">Data de Cadastro</p>
                                <p style="font-size:13px;font-weight:600;color:#475569;margin:0;">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- IMAGEM (thumbnail rápido) --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Imagem do Produto
                    </div>
                    <div class="nx-sidebar-card-body" style="padding:12px;">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="nx-product-thumb">
                            <button type="button" wire:click="$set('image', null)" class="nx-btn nx-btn-ghost" style="width:100%;margin-top:8px;font-size:12px;justify-content:center;">
                                Remover imagem
                            </button>
                        @elseif($isEditing && $product?->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="nx-product-thumb">
                        @else
                            <div class="nx-product-thumb-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <p>Sem imagem</p>
                            </div>
                            <button type="button" wire:click="$set('activeTab', 'imagens')" class="nx-btn nx-btn-ghost" style="width:100%;margin-top:8px;font-size:12px;justify-content:center;">
                                Adicionar imagem
                            </button>
                        @endif
                    </div>
                </div>

                {{-- DESTAQUES --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Destaques
                    </div>
                    <div class="nx-sidebar-card-body">
                        <div class="nx-tags-wrap" style="margin-bottom:8px;">
                            @foreach($form->highlights as $i => $hl)
                                <span class="nx-tag nx-tag--blue">
                                    {{ $hl }}
                                    <button type="button" wire:click="removeHighlight({{ $i }})" class="nx-tag-remove">×</button>
                                </span>
                            @endforeach
                        </div>
                        <div class="nx-tag-input-row">
                            <input type="text" wire:model.live="highlightInput" placeholder="Ex: Tela Full HD" wire:keydown.enter.prevent="addHighlight" style="font-size:12.5px;">
                            <button type="button" wire:click="addHighlight" class="nx-tag-add-btn">+</button>
                        </div>
                        <small>Pressione Enter para adicionar</small>
                    </div>
                </div>

                {{-- TAGS --}}
                <div class="nx-sidebar-card" style="margin-top:12px;">
                    <div class="nx-sidebar-card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        Tags / Palavras-chave
                    </div>
                    <div class="nx-sidebar-card-body">
                        <div class="nx-tags-wrap" style="margin-bottom:8px;">
                            @foreach($form->tags as $i => $tag)
                                <span class="nx-tag nx-tag--gray">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $i }})" class="nx-tag-remove">×</button>
                                </span>
                            @endforeach
                        </div>
                        <div class="nx-tag-input-row">
                            <input type="text" wire:model.live="tagInput" placeholder="Ex: monitor, led" wire:keydown.enter.prevent="addTag" style="font-size:12.5px;">
                            <button type="button" wire:click="addTag" class="nx-tag-add-btn">+</button>
                        </div>
                        <small>Minúsculas • Pressione Enter para adicionar</small>
                    </div>
                </div>

            </aside>{{-- /nx-product-sidebar --}}
        </div>{{-- /nx-product-layout --}}

        {{-- ═══════ FOOTER ═══════ --}}
        <div class="nx-product-footer" style="max-width:1200px;margin:24px auto 0;">
            <div class="nx-form-footer-left">
                <a href="{{ route('products.index') }}" class="nx-btn nx-btn-ghost" wire:navigate>Cancelar</a>
            </div>
            @if(!$isEditing)
                <button type="button" wire:click="saveAndNew" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-secondary">
                    <svg wire:loading.remove wire:target="saveAndNew" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/><line x1="12" y1="10" x2="12" y2="16"/><line x1="9" y1="13" x2="15" y2="13"/></svg>
                    <svg wire:loading wire:target="saveAndNew" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Salvar e Novo
                </button>
            @endif
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="nx-btn--loading" class="nx-btn nx-btn-primary">
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <svg wire:loading wire:target="save" class="nx-spin" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ $isEditing ? 'Salvar Alterações' : 'Salvar Produto' }}
            </button>
        </div>

    </form>

    {{-- ═══════════════════════════════════════════════════════════
         MODAL: CRIAR CATEGORIA
    ═══════════════════════════════════════════════════════════ --}}
    @if($showCategoryModal)
        <div class="nx-modal-overlay" wire:click="closeCategoryModal">
            <div class="nx-modal" wire:click.stop style="max-width:500px;">
                <div class="nx-modal-header">
                    <h3 class="nx-modal-title">Nova Categoria</h3>
                    <button type="button" wire:click="closeCategoryModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Nome da Categoria <span class="nx-required">*</span></label>
                        <input type="text" wire:model="newCategoryName" placeholder="Ex: Eletrônicos" maxlength="100" autofocus>
                        @error('newCategoryName') <span class="nx-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="nx-field" style="margin-bottom:16px;">
                        <label>Descrição</label>
                        <textarea wire:model="newCategoryDescription" rows="3" placeholder="Descrição opcional da categoria"></textarea>
                    </div>
                    <div class="nx-field">
                        <label>Cor</label>
                        <input type="color" wire:model="newCategoryColor" style="height:40px;">
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="closeCategoryModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="saveCategory" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                        <span wire:loading.remove wire:target="saveCategory">Criar Categoria</span>
                        <span wire:loading wire:target="saveCategory">Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         MODAL: CRIAR UNIDADE DE MEDIDA
    ═══════════════════════════════════════════════════════════ --}}
    @if($showUnitModal)
        <div class="nx-modal-overlay" wire:click="closeUnitModal">
            <div class="nx-modal" wire:click.stop style="max-width:500px;">
                <div class="nx-modal-header">
                    <h3 class="nx-modal-title">Nova Unidade de Medida</h3>
                    <button type="button" wire:click="closeUnitModal" class="nx-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="nx-modal-body">
                    <div class="grid grid-2" style="margin-bottom:16px;">
                        <div class="nx-field">
                            <label>Nome <span class="nx-required">*</span></label>
                            <input type="text" wire:model="newUnitName" placeholder="Ex: Quilograma" maxlength="50" autofocus>
                            @error('newUnitName') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="nx-field">
                            <label>Sigla <span class="nx-required">*</span></label>
                            <input type="text" wire:model="newUnitAbbreviation" placeholder="Ex: KG" maxlength="10" style="text-transform:uppercase;">
                            @error('newUnitAbbreviation') <span class="nx-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="nx-field">
                        <label>Descrição</label>
                        <textarea wire:model="newUnitDescription" rows="3" placeholder="Descrição opcional da unidade"></textarea>
                    </div>
                </div>
                <div class="nx-modal-footer">
                    <button type="button" wire:click="closeUnitModal" class="nx-btn nx-btn-ghost">Cancelar</button>
                    <button type="button" wire:click="saveUnit" wire:loading.attr="disabled" class="nx-btn nx-btn-primary">
                        <span wire:loading.remove wire:target="saveUnit">Criar Unidade</span>
                        <span wire:loading wire:target="saveUnit">Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

