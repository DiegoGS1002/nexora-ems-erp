<div class="nx-pf-page" x-data="pedidoForm()">

    {{-- ── TOPBAR ───────────────────────────────────────────────── --}}
    <div class="nx-pf-topbar">
        <div class="nx-pf-topbar-left">
            <a href="{{ route('vendas.pedidos') }}" wire:navigate class="nx-pf-back-btn" title="Voltar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="nx-pf-topbar-info">
                <div class="nx-pf-topbar-title">
                    @if($orderId)
                        Editar Pedido
                        @if($order)
                            <span class="nx-pf-order-num">#{{ $order->order_number }}</span>
                            <span class="nx-so-badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                        @endif
                    @else
                        Novo Pedido de Venda
                    @endif
                </div>
                <div class="nx-pf-topbar-sub">Preencha os dados e finalize para gerar a NF-e</div>
            </div>
        </div>
        <div class="nx-pf-topbar-actions">
            <button type="button" wire:click="save" wire:loading.attr="disabled" class="nx-btn nx-btn-ghost nx-btn-sm">
                <span wire:loading.remove wire:target="save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13"/><polyline points="7 3 7 8 15 8"/></svg>
                    Salvar Rascunho
                </span>
                <span wire:loading wire:target="save">Salvando…</span>
            </button>
            <button type="button" wire:click="openFiscalPanel" wire:loading.attr="disabled" class="nx-btn nx-btn-primary nx-btn-sm">
                <span wire:loading.remove wire:target="openFiscalPanel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Finalizar Pedido
                </span>
                <span wire:loading wire:target="openFiscalPanel">Processando…</span>
            </button>
        </div>
    </div>

    {{-- ── FLASH ─────────────────────────────────────────────────── --}}
    @session('message')
        <div class="nx-pf-alert nx-pf-alert--success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="nx-pf-alert nx-pf-alert--error" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,8000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $value }}
        </div>
    @endsession
    @session('fiscal_applied')
        <div class="nx-pf-alert nx-pf-alert--info" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
            {{ $value }}
        </div>
    @endsession

    {{-- ── BODY: SIDEBAR + FORM ─────────────────────────────────── --}}
    <div class="nx-pf-body">

        {{-- ── LEFT: TABS NAV ──────────────────────────────────── --}}
        <nav class="nx-pf-sidenav">
            @php
                $tabs = [
                    ['id'=>'geral',      'label'=>'Geral',       'icon'=>'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2'],
                    ['id'=>'itens',      'label'=>'Itens',        'icon'=>'M4 6h16M4 10h16M4 14h16M4 18h16'],
                    ['id'=>'totais',     'label'=>'Totais',       'icon'=>'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
                    ['id'=>'logistica',  'label'=>'Logística',    'icon'=>'M1 3h15v13H1zM16 8h4l3 3v5h-7V8z'],
                    ['id'=>'pagamento',  'label'=>'Pagamento',    'icon'=>'M21 4H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM1 10h22'],
                    ['id'=>'enderecos',  'label'=>'Endereços',    'icon'=>'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z'],
                    ['id'=>'fiscal',     'label'=>'Fiscal',       'icon'=>'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
                    ['id'=>'historico',  'label'=>'Histórico',    'icon'=>'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button type="button"
                    wire:click="$set('activeTab','{{ $tab['id'] }}')"
                    class="nx-pf-sidenav-item {{ $activeTab === $tab['id'] ? 'nx-pf-sidenav-item--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="{{ $tab['icon'] }}"/>
                    </svg>
                    <span>{{ $tab['label'] }}</span>
                    @if($tab['id'] === 'itens')
                        <span class="nx-pf-sidenav-badge">{{ count($orderItems) }}</span>
                    @endif
                </button>
            @endforeach
        </nav>

        {{-- ── RIGHT: FORM CONTENT ─────────────────────────────── --}}
        <div class="nx-pf-content">

            {{-- ════════════════════ TAB: GERAL ════════════════════ --}}
            @if($activeTab === 'geral')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Informações Gerais</h2>
                    <p>Dados básicos do pedido de venda</p>
                </div>
                <div class="nx-pf-grid-2">

                    <div class="nx-field nx-pf-field--full">
                        <label>Cliente <span class="nx-required">*</span></label>
                        <select wire:model.live="client_id" class="{{ $errors->has('client_id') ? 'nx-field-input--error' : '' }}">
                            <option value="">— Selecionar cliente —</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->social_name ?? $c->name }} — {{ $c->taxNumber ?? '—' }}</option>
                            @endforeach
                        </select>
                        @error('client_id') <small class="nx-field-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="nx-field">
                        <label>Vendedor</label>
                        <select wire:model="seller_id">
                            <option value="">— Selecionar vendedor —</option>
                            @foreach($sellers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Status</label>
                        <select wire:model="status">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Data do Pedido</label>
                        <input type="datetime-local" wire:model="order_date">
                    </div>

                    <div class="nx-field">
                        <label>Prev. Entrega</label>
                        <input type="date" wire:model="expected_delivery_date">
                    </div>

                    <div class="nx-field">
                        <label>Tipo de Operação</label>
                        <select wire:model="operation_type">
                            <option value="">— Selecionar —</option>
                            @foreach($operacoes as $op)
                                <option value="{{ $op->value }}">{{ $op->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Canal de Venda</label>
                        <select wire:model="sales_channel">
                            <option value="">— Selecionar —</option>
                            @foreach($canais as $canal)
                                <option value="{{ $canal->value }}">{{ $canal->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Origem</label>
                        <select wire:model="origin">
                            <option value="">— Selecionar —</option>
                            @foreach($origens as $orig)
                                <option value="{{ $orig->value }}">{{ $orig->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Tabela de Preço</label>
                        <select wire:model="price_table_id">
                            <option value="">— Nenhuma —</option>
                            @foreach($priceTables as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="nx-field">
                        <label>Condição de Pagamento</label>
                        <input type="text" wire:model="payment_condition" placeholder="Ex: 30/60/90 dias">
                    </div>


                    <div class="nx-field nx-pf-field--full">
                        <label>Observações Internas</label>
                        <textarea wire:model="internal_notes" rows="2" placeholder="Notas internas (não aparecem na NF-e)"></textarea>
                    </div>

                    <div class="nx-field nx-pf-field--full">
                        <label>Observações ao Cliente</label>
                        <textarea wire:model="customer_notes" rows="2" placeholder="Mensagem para o cliente"></textarea>
                    </div>

                </div>
            </div>
            @endif

            {{-- ════════════════════ TAB: ITENS ════════════════════ --}}
            @if($activeTab === 'itens')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Itens do Pedido</h2>
                    <p>Adicione os produtos e configure as quantidades e preços</p>
                </div>

                {{-- Busca de produto --}}
                <div class="nx-pf-product-search" x-data="{open: false}">
                    <div class="nx-pf-product-search-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" wire:model.live.debounce.300ms="searchProduct"
                            placeholder="Buscar produto por nome, código ou EAN… (digite '.' para listar todos)"
                            @focus="open=true" @blur="setTimeout(()=>open=false,200)">
                    </div>
                    @if(count($searchResults))
                    <div class="nx-pf-product-dropdown">
                        @foreach($searchResults as $prod)
                            <button type="button" wire:click="addProduct('{{ $prod['id'] }}')" class="nx-pf-product-item">
                                <div class="nx-pf-product-item-info">
                                    <span class="nx-pf-product-name">{{ $prod['name'] }}</span>
                                    <span class="nx-pf-product-code">{{ $prod['product_code'] ?? '' }}</span>
                                </div>
                                <span class="nx-pf-product-price">R$ {{ number_format($prod['sale_price'] ?? 0, 2, ',', '.') }}</span>
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @error('searchProduct') <small class="nx-field-error">{{ $message }}</small> @enderror
                @error('orderItems') <small class="nx-field-error">{{ $message }}</small> @enderror

                {{-- Items table --}}
                @if(count($orderItems))
                <div class="nx-pf-items-table">
                    <table>
                        <thead>
                            <tr>
                                <th style="min-width:200px">Produto</th>
                                <th style="width:70px">Unid.</th>
                                <th style="width:110px">Qtd.</th>
                                <th style="width:120px">Preço Unit.</th>
                                <th style="width:90px">Desc. %</th>
                                <th style="width:120px">Total</th>
                                <th style="width:80px">CFOP</th>
                                <th style="width:90px">NCM</th>
                                <th style="width:60px">CST</th>
                                <th style="width:80px">ICMS%</th>
                                <th style="width:70px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderItems as $idx => $item)
                            <tr>
                                <td>
                                    <div class="nx-pf-item-name">{{ $item['product_name'] }}</div>
                                    @if($item['sku'])
                                        <div class="nx-pf-item-sku">{{ $item['sku'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <input type="text" wire:model.lazy="orderItems.{{ $idx }}.unit" class="nx-pf-cell-input" style="width:60px">
                                </td>
                                <td>
                                    <input type="number" wire:model.live="orderItems.{{ $idx }}.quantity" min="0.001" step="0.001" class="nx-pf-cell-input">
                                </td>
                                <td>
                                    <input type="number" wire:model.live="orderItems.{{ $idx }}.unit_price" min="0" step="0.01" class="nx-pf-cell-input">
                                </td>
                                <td>
                                    <input type="number" wire:model.live="orderItems.{{ $idx }}.discount_percent" min="0" max="100" step="0.01" class="nx-pf-cell-input">
                                </td>
                                <td class="nx-pf-item-total">
                                    @php
                                        $qty  = (float)($item['quantity'] ?? 0);
                                        $prc  = (float)($item['unit_price'] ?? 0);
                                        $dp   = (float)($item['discount_percent'] ?? 0);
                                        $disc = ($dp > 0) ? ($qty * $prc) * ($dp/100) : (float)($item['discount'] ?? 0);
                                        $line = ($qty * $prc) - $disc;
                                    @endphp
                                    R$ {{ number_format($line, 2, ',', '.') }}
                                </td>
                                <td>
                                    <input type="text" wire:model.lazy="orderItems.{{ $idx }}.cfop" maxlength="4" class="nx-pf-cell-input" style="width:65px">
                                </td>
                                <td>
                                    <input type="text" wire:model.lazy="orderItems.{{ $idx }}.ncm" maxlength="8" class="nx-pf-cell-input"
                                        placeholder="00000000"
                                        style="width:80px;{{ empty($item['ncm']) ? 'border-color:#F59E0B' : '' }}">
                                </td>
                                <td>
                                    <input type="text" wire:model.lazy="orderItems.{{ $idx }}.cst" maxlength="3" class="nx-pf-cell-input" style="width:55px">
                                </td>
                                <td>
                                    <input type="number" wire:model.lazy="orderItems.{{ $idx }}.icms_percent" min="0" max="100" step="0.01" class="nx-pf-cell-input" style="width:70px">
                                </td>
                                <td>
                                    <button type="button" wire:click="removeItem({{ $idx }})" class="nx-pf-remove-btn" title="Remover item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="nx-pf-items-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="color:#CBD5E1;margin-bottom:12px"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <p>Nenhum produto adicionado</p>
                    <small>Use a busca acima para adicionar produtos</small>
                </div>
                @endif
            </div>
            @endif

            {{-- ════════════════════ TAB: TOTAIS ═══════════════════ --}}
            @if($activeTab === 'totais')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Descontos e Totais</h2>
                </div>
                <div class="nx-pf-totals-grid">
                    <div class="nx-pf-totals-inputs">
                        <div class="nx-pf-grid-2">
                            <div class="nx-field">
                                <label>Desconto Geral (R$)</label>
                                <input type="number" wire:model.lazy="discount_amount" min="0" step="0.01" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Acréscimos (R$)</label>
                                <input type="number" wire:model.lazy="additions_amount" min="0" step="0.01" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Frete (R$)</label>
                                <input type="number" wire:model.lazy="shipping_amount" min="0" step="0.01" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Seguro (R$)</label>
                                <input type="number" wire:model.lazy="insurance_amount" min="0" step="0.01" placeholder="0,00">
                            </div>
                            <div class="nx-field">
                                <label>Outras Despesas (R$)</label>
                                <input type="number" wire:model.lazy="other_expenses" min="0" step="0.01" placeholder="0,00">
                            </div>
                        </div>
                    </div>
                    <div class="nx-pf-totals-summary">
                        <div class="nx-pf-total-row">
                            <span>Subtotal dos Itens</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-pf-total-row">
                            <span>Desconto</span>
                            <span style="color:#EF4444">- R$ {{ number_format($discount_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-pf-total-row">
                            <span>Frete</span>
                            <span>R$ {{ number_format($shipping_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-pf-total-row">
                            <span>Acréscimos</span>
                            <span>R$ {{ number_format($additions_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-pf-total-row nx-pf-total-row--taxes">
                            <span>Impostos Est.</span>
                            <span style="color:#F59E0B">R$ {{ number_format($taxEstimate, 2, ',', '.') }}</span>
                        </div>
                        <div class="nx-pf-total-row nx-pf-total-row--grand">
                            <span>TOTAL GERAL</span>
                            <span>R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ═════════════════ TAB: LOGÍSTICA ═══════════════════ --}}
            @if($activeTab === 'logistica')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Logística e Transporte</h2>
                </div>
                <div class="nx-pf-grid-2">
                    <div class="nx-field">
                        <label>Transportadora</label>
                        <select wire:model="carrier_id">
                            <option value="">— Nenhuma —</option>
                            @foreach($carriers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="nx-field">
                        <label>Tipo de Frete</label>
                        <select wire:model="freight_type">
                            <option value="">— Selecionar —</option>
                            @foreach($tiposFrete as $tf)
                                <option value="{{ $tf->value }}">{{ $tf->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="nx-field">
                        <label>Peso Bruto (kg)</label>
                        <input type="number" wire:model="gross_weight" min="0" step="0.001" placeholder="0.000">
                    </div>
                    <div class="nx-field">
                        <label>Peso Líquido (kg)</label>
                        <input type="number" wire:model="net_weight" min="0" step="0.001" placeholder="0.000">
                    </div>
                    <div class="nx-field">
                        <label>Volumes</label>
                        <input type="number" wire:model="volumes" min="0" step="1" placeholder="1">
                    </div>
                    <div class="nx-field">
                        <label>Código de Rastreio</label>
                        <input type="text" wire:model="tracking_code" placeholder="AA000000000BR">
                    </div>
                    <div class="nx-field nx-pf-field--full">
                        <label>Obs. de Entrega</label>
                        <textarea wire:model="delivery_notes" rows="2" placeholder="Instruções para entrega…"></textarea>
                    </div>
                </div>
            </div>
            @endif

            {{-- ═════════════════ TAB: PAGAMENTO ═══════════════════ --}}
            @if($activeTab === 'pagamento')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Forma de Pagamento</h2>
                </div>
                <div class="nx-pf-grid-2">
                    <div class="nx-field">
                        <label>Forma de Pagamento</label>
                        <select wire:model="payment_method">
                            <option value="">— Selecionar —</option>
                            <option value="01">Dinheiro</option>
                            <option value="02">Cheque</option>
                            <option value="03">Cartão de Crédito</option>
                            <option value="04">Cartão de Débito</option>
                            <option value="05">Crédito Loja</option>
                            <option value="10">Vale Alimentação</option>
                            <option value="11">Vale Refeição</option>
                            <option value="15">Boleto Bancário</option>
                            <option value="17">PIX</option>
                            <option value="90">Sem Pagamento</option>
                            <option value="99">Outros</option>
                        </select>
                    </div>
                    <div class="nx-field">
                        <label>Condição de Pagamento</label>
                        <input type="text" wire:model="payment_condition" placeholder="Ex: 30/60/90 dias, À vista…">
                    </div>
                    <div class="nx-field">
                        <label>Nº de Parcelas</label>
                        <input type="number" wire:model="installments_qty" min="1" max="60" step="1">
                    </div>
                </div>
            </div>
            @endif

            {{-- ═════════════════ TAB: ENDEREÇOS ═══════════════════ --}}
            @if($activeTab === 'enderecos')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Endereços</h2>
                </div>

                <div class="nx-pf-section-block">
                    <h3 class="nx-pf-block-title">Endereço de Cobrança</h3>
                    @include('livewire.vendas._address-fields', ['prefix' => 'billing'])
                </div>

                <div class="nx-pf-section-block" style="margin-top:24px">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                        <h3 class="nx-pf-block-title" style="margin-bottom:0">Endereço de Entrega</h3>
                        <label class="nx-pf-checkbox-label">
                            <input type="checkbox" wire:model.live="same_billing_delivery">
                            Mesmo que cobrança
                        </label>
                    </div>
                    @if(!$same_billing_delivery)
                        @include('livewire.vendas._address-fields', ['prefix' => 'delivery'])
                    @else
                        <p style="font-size:13px;color:#94A3B8;padding:12px;background:#F8FAFC;border-radius:8px;border:1px dashed #E2E8F0">
                            Usando o mesmo endereço de cobrança para entrega.
                        </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- ════════════════════ TAB: FISCAL ═══════════════════ --}}
            @if($activeTab === 'fiscal')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Configuração Fiscal</h2>
                    <p>Defina o tipo de operação e aplique configurações fiscais aos itens</p>
                </div>

                <div class="nx-pf-grid-2">
                    <div class="nx-field nx-pf-field--full">
                        <label>Tipo de Operação Fiscal (CFOP / CST)</label>
                        <div style="display:flex;gap:8px">
                            <select wire:model="tipo_operacao_fiscal_id" style="flex:1">
                                <option value="">— Selecionar —</option>
                                @foreach($tiposOperacaoFiscal as $to)
                                    <option value="{{ $to->id }}">{{ $to->codigo }} — {{ $to->descricao }} (CFOP: {{ $to->cfop }})</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="applyTipoOperacao" class="nx-btn nx-btn-ghost nx-btn-sm">
                                Aplicar a Todos
                            </button>
                        </div>
                    </div>
                    <div class="nx-field nx-pf-field--full">
                        <label>Observações Fiscais (infAdic)</label>
                        <textarea wire:model="fiscal_notes_obs" rows="3" placeholder="Informações adicionais para a NF-e…"></textarea>
                    </div>
                </div>

                {{-- Fiscal items grid --}}
                @if(count($orderItems))
                <div style="margin-top:20px">
                    <h3 style="font-size:13px;font-weight:600;color:#475569;margin-bottom:12px">Dados Fiscais por Item</h3>
                    <div class="nx-pf-items-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>NCM</th>
                                    <th>CFOP</th>
                                    <th>CST</th>
                                    <th>CSOSN</th>
                                    <th>Orig.</th>
                                    <th>ICMS%</th>
                                    <th>IPI%</th>
                                    <th>PIS%</th>
                                    <th>COFINS%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $idx => $item)
                                <tr>
                                    <td><div class="nx-pf-item-name" style="font-size:12px">{{ $item['product_name'] }}</div></td>
                                    <td><input type="text" wire:model.lazy="orderItems.{{ $idx }}.ncm" maxlength="8" class="nx-pf-cell-input" placeholder="00000000" style="{{ empty($item['ncm']) ? 'border-color:#F59E0B;' : '' }}"></td>
                                    <td><input type="text" wire:model.lazy="orderItems.{{ $idx }}.cfop" maxlength="4" class="nx-pf-cell-input"></td>
                                    <td><input type="text" wire:model.lazy="orderItems.{{ $idx }}.cst" maxlength="3" class="nx-pf-cell-input"></td>
                                    <td><input type="text" wire:model.lazy="orderItems.{{ $idx }}.csosn" maxlength="3" class="nx-pf-cell-input"></td>
                                    <td>
                                        <select wire:model.lazy="orderItems.{{ $idx }}.origin" class="nx-pf-cell-input" style="width:60px">
                                            @foreach(['0'=>'0-Nac','1'=>'1-Ext','2'=>'2-Ext','3'=>'3-Nac','4'=>'4-Nac','5'=>'5-Nac','6'=>'6-Ext','7'=>'7-Ext','8'=>'8-Nac'] as $v => $l)
                                                <option value="{{ $v }}" @selected(($item['origin']??'0') == $v)>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" wire:model.lazy="orderItems.{{ $idx }}.icms_percent" min="0" step="0.01" class="nx-pf-cell-input" style="width:65px"></td>
                                    <td><input type="number" wire:model.lazy="orderItems.{{ $idx }}.ipi_percent" min="0" step="0.01" class="nx-pf-cell-input" style="width:65px"></td>
                                    <td><input type="number" wire:model.lazy="orderItems.{{ $idx }}.pis_percent" min="0" step="0.01" class="nx-pf-cell-input" style="width:65px"></td>
                                    <td><input type="number" wire:model.lazy="orderItems.{{ $idx }}.cofins_percent" min="0" step="0.01" class="nx-pf-cell-input" style="width:65px"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- ════════════════════ TAB: HISTÓRICO ════════════════ --}}
            @if($activeTab === 'historico')
            <div class="nx-pf-section">
                <div class="nx-pf-section-header">
                    <h2>Histórico e Linha do Tempo</h2>
                </div>
                @if($orderId && $order && $order->logs->isNotEmpty())
                    <div class="nx-pf-timeline">
                        @foreach($order->logs->sortByDesc('created_at') as $log)
                        <div class="nx-pf-timeline-item">
                            <div class="nx-pf-timeline-dot"></div>
                            <div class="nx-pf-timeline-content">
                                <div class="nx-pf-timeline-meta">
                                    <span class="nx-pf-timeline-action">{{ $log->description ?? $log->action }}</span>
                                    <span class="nx-pf-timeline-time">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($log->user)
                                    <div class="nx-pf-timeline-user">{{ $log->user->name }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @elseif($orderId)
                    <p style="color:#94A3B8;font-size:13px;padding:20px 0">Nenhum evento registrado ainda.</p>
                @else
                    <p style="color:#94A3B8;font-size:13px;padding:20px 0">Salve o pedido para iniciar o histórico.</p>
                @endif
            </div>
            @endif

        </div>{{-- /nx-pf-content --}}

        {{-- ── RIGHT SIDEBAR: SUMMARY ───────────────────────────── --}}
        <aside class="nx-pf-sidebar">

            {{-- NF-e status (se tiver) --}}
            @if($orderId && $order && $order->fiscalNotes->isNotEmpty())
            <div class="nx-pf-sidebar-card">
                <div class="nx-pf-sidebar-card-title">Notas Fiscais</div>
                @foreach($order->fiscalNotes as $nf)
                <div class="nx-pf-nf-row">
                    <span class="nx-so-badge {{ $nf->status->badgeClass() }}" style="font-size:10px">{{ $nf->status->label() }}</span>
                    <span style="font-size:12px;color:#475569">NF #{{ $nf->invoice_number }}</span>
                    @if($nf->status === \App\Enums\FiscalNoteStatus::Authorized)
                        <a href="{{ route('api.nfe.danfe', $nf->id) }}" target="_blank" class="nx-pf-nf-link" title="DANFE">PDF</a>
                        <a href="{{ route('api.nfe.xml', $nf->id) }}" target="_blank" class="nx-pf-nf-link" title="XML">XML</a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Order Summary --}}
            <div class="nx-pf-sidebar-card">
                <div class="nx-pf-sidebar-card-title">Resumo do Pedido</div>
                <div class="nx-pf-summary-rows">
                    <div class="nx-pf-summary-row">
                        <span>Itens</span>
                        <span>{{ count($orderItems) }}</span>
                    </div>
                    <div class="nx-pf-summary-row">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-pf-summary-row">
                        <span>Desconto</span>
                        <span style="color:#EF4444">-R$ {{ number_format($discount_amount ?: 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-pf-summary-row">
                        <span>Frete</span>
                        <span>R$ {{ number_format($shipping_amount ?: 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="nx-pf-summary-row nx-pf-summary-row--total">
                        <span>TOTAL</span>
                        <span>R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            @if($orderId && $order)
            <div class="nx-pf-sidebar-card">
                <div class="nx-pf-sidebar-card-title">Ações Rápidas</div>
                <div style="display:flex;flex-direction:column;gap:6px">
                    @if($order->status->canInvoice())
                        <button type="button" wire:click="openFiscalPanel" class="nx-btn nx-btn-primary nx-btn-sm" style="width:100%">
                            Finalizar / Faturar
                        </button>
                    @endif
                    @if($order->status === \App\Enums\SalesOrderStatus::NfRejeitada)
                        <button type="button" wire:click="openFiscalPanel" class="nx-btn nx-btn-ghost nx-btn-sm" style="width:100%;color:#EF4444">
                            Corrigir e Retransmitir
                        </button>
                    @endif
                </div>
            </div>
            @endif

        </aside>

    </div>{{-- /nx-pf-body --}}


    {{-- ═══════════════════════════════════════════════════════════════
         FISCAL PANEL — DRAWER / OVERLAY
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($showFiscalPanel)
    <div class="nx-pf-fiscal-overlay" wire:click.self="closeFiscalPanel">
        <div class="nx-pf-fiscal-drawer" x-data x-trap.noscroll="true">

            <div class="nx-pf-fiscal-drawer-header">
                <div>
                    <h2>Finalizar Pedido</h2>
                    <p>Escolha como deseja finalizar este pedido.</p>
                </div>
                <button type="button" wire:click="closeFiscalPanel" class="nx-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Warnings --}}
            @if(count($fiscalWarnings))
            <div class="nx-pf-fiscal-warnings">
                <div class="nx-pf-fiscal-warnings-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Alertas Fiscais
                </div>
                @foreach($fiscalWarnings as $warn)
                <div class="nx-pf-fiscal-warning-item nx-pf-fiscal-warning--{{ $warn['type'] }}">
                    {{ $warn['msg'] }}
                </div>
                @endforeach
            </div>
            @endif

            {{-- Summary --}}
            <div class="nx-pf-fiscal-summary">
                <div class="nx-pf-fiscal-summary-row">
                    <span>Itens</span><span>{{ count($orderItems) }}</span>
                </div>
                <div class="nx-pf-fiscal-summary-row">
                    <span>Valor Total</span><span style="font-weight:700;color:#0F172A">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                </div>
                <div class="nx-pf-fiscal-summary-row">
                    <span>Impostos Estimados</span><span style="color:#F59E0B">R$ {{ number_format($taxEstimate, 2, ',', '.') }}</span>
                </div>
            </div>

            {{-- Action Cards --}}
            <div class="nx-pf-fiscal-actions">

                {{-- Ação 1: ROMANEIO (gerencial) --}}
                <div class="nx-pf-fiscal-action-card" wire:click="finalizarRomaneio" style="cursor:pointer">
                    <div class="nx-pf-fac-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/></svg>
                    </div>
                    <div class="nx-pf-fac-info">
                        <div class="nx-pf-fac-title">Romaneio</div>
                        <div class="nx-pf-fac-desc">Fechamento gerencial do pedido (sem emissão de NF-e)</div>
                    </div>
                    <div class="nx-pf-fac-status">
                        <span class="nx-pf-status-pill" style="background:#DBEAFE;color:#1E40AF">Gerencial</span>
                    </div>
                </div>

                {{-- Ação 2: NOTA FISCAL (abre nova aba) --}}
                @php $hasErrors = collect($fiscalWarnings)->contains(fn($w) => $w['type'] === 'error'); @endphp
                <div class="nx-pf-fiscal-action-card {{ $hasErrors ? 'nx-pf-fac--disabled' : '' }}"
                    @if(!$hasErrors) wire:click="gerarNotaFiscal" style="cursor:pointer" @endif>
                    <div class="nx-pf-fac-icon" style="background:rgba(16,185,129,0.1);color:#10B981">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div class="nx-pf-fac-info">
                        <div class="nx-pf-fac-title">Nota Fiscal (NF-e)</div>
                        <div class="nx-pf-fac-desc">
                            @if($hasErrors)
                                Corrija os erros fiscais antes de continuar.
                            @else
                                Gera NF-e e abre em nova aba para visualização e transmissão
                            @endif
                        </div>
                    </div>
                    <div class="nx-pf-fac-status">
                        <span class="nx-pf-status-pill" style="background:{{ $hasErrors ? '#FEE2E2' : '#ECFDF5' }};color:{{ $hasErrors ? '#EF4444' : '#059669' }}">
                            {{ $hasErrors ? 'Bloqueado' : 'SEFAZ' }}
                        </span>
                    </div>
                </div>

                {{-- Ação 3: PEDIDO (impressão simples) --}}
                <div class="nx-pf-fiscal-action-card" wire:click="imprimirPedido" style="cursor:pointer">
                    <div class="nx-pf-fac-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    </div>
                    <div class="nx-pf-fac-info">
                        <div class="nx-pf-fac-title">Pedido (Impressão)</div>
                        <div class="nx-pf-fac-desc">Imprime apenas o pedido para uso interno</div>
                    </div>
                    <div class="nx-pf-fac-status">
                        <span class="nx-pf-status-pill" style="background:#FEF3C7;color:#D97706">PDF</span>
                    </div>
                </div>

            </div>{{-- /fiscal-actions --}}

        </div>
    </div>

    {{-- JavaScript para abrir janelas --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-nfe-window', (event) => {
                window.open(`/fiscal/nfe/${event.noteId}/editar`, '_blank');
            });
            Livewire.on('open-print-window', (event) => {
                window.open(`/vendas/pedidos/${event.orderId}/imprimir`, '_blank');
            });
        });
    </script>
    @endif

</div>

@push('styles')
<style>
/* ═══════════════════════════════════════════
   PEDIDO FORM — FULL PAGE
═══════════════════════════════════════════ */
.nx-pf-page { display:flex; flex-direction:column; height:100vh; background:#F8FAFC; overflow:hidden; }

/* Topbar */
.nx-pf-topbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 20px; background:#fff; border-bottom:1px solid #E2E8F0;
    position:sticky; top:0; z-index:20; box-shadow:0 1px 3px rgba(0,0,0,0.04);
    flex-shrink:0;
}
.nx-pf-topbar-left { display:flex; align-items:center; gap:12px; }
.nx-pf-back-btn {
    display:flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:8px; border:1px solid #E2E8F0;
    color:#64748B; background:#fff; transition:all .15s;
    text-decoration:none; flex-shrink:0;
}
.nx-pf-back-btn:hover { background:#F1F5F9; border-color:#CBD5E1; }
.nx-pf-topbar-info { display:flex; flex-direction:column; gap:2px; }
.nx-pf-topbar-title {
    font-size:15px; font-weight:700; color:#0F172A;
    display:flex; align-items:center; gap:8px; flex-wrap:wrap;
}
.nx-pf-order-num {
    font-size:13px; color:#6366F1; font-weight:600;
    background:#EEF2FF; padding:2px 8px; border-radius:6px;
}
.nx-pf-topbar-sub { font-size:12px; color:#94A3B8; }
.nx-pf-topbar-actions { display:flex; align-items:center; gap:8px; flex-shrink:0; }

/* Alerts */
.nx-pf-alert {
    display:flex; align-items:center; gap:8px; padding:10px 20px;
    font-size:13px; border-bottom:1px solid transparent; flex-shrink:0;
}
.nx-pf-alert--success { background:#F0FDF4; border-color:#BBF7D0; color:#15803D; }
.nx-pf-alert--error   { background:#FEF2F2; border-color:#FECACA; color:#DC2626; }
.nx-pf-alert--info    { background:#EFF6FF; border-color:#BFDBFE; color:#1D4ED8; }

/* Body layout */
.nx-pf-body { display:flex; flex:1; min-height:0; overflow:hidden; }

/* Side nav */
.nx-pf-sidenav {
    width:170px; flex-shrink:0; background:#fff; border-right:1px solid #E2E8F0;
    padding:12px 8px; display:flex; flex-direction:column; gap:2px; overflow-y:auto;
}
.nx-pf-sidenav-item {
    display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px;
    font-size:13px; color:#64748B; font-weight:500; border:none; background:none;
    cursor:pointer; text-align:left; transition:all .15s; position:relative; width:100%;
}
.nx-pf-sidenav-item svg { flex-shrink:0; }
.nx-pf-sidenav-item:hover { background:#F1F5F9; color:#334155; }
.nx-pf-sidenav-item--active { background:#EEF2FF; color:#4F46E5; font-weight:600; }
.nx-pf-sidenav-badge {
    margin-left:auto; min-width:18px; height:18px; border-radius:9px;
    background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:700;
    display:flex; align-items:center; justify-content:center; padding:0 5px;
}

/* Content area */
.nx-pf-content { flex:1; overflow-y:auto; padding:20px; }

/* Right sidebar */
.nx-pf-sidebar {
    width:240px; flex-shrink:0; background:#fff; border-left:1px solid #E2E8F0;
    padding:16px 12px; display:flex; flex-direction:column; gap:12px; overflow-y:auto;
}
.nx-pf-sidebar-card { background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:12px; }
.nx-pf-sidebar-card-title {
    font-size:11px; font-weight:700; color:#94A3B8;
    text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px;
}
.nx-pf-summary-rows { display:flex; flex-direction:column; gap:6px; }
.nx-pf-summary-row {
    display:flex; justify-content:space-between; align-items:center;
    font-size:12px; color:#475569;
}
.nx-pf-summary-row--total {
    font-weight:700; color:#0F172A; font-size:14px;
    border-top:1px solid #E2E8F0; padding-top:8px; margin-top:4px;
}
.nx-pf-nf-row {
    display:flex; align-items:center; gap:6px; padding:4px 0;
    font-size:12px; flex-wrap:wrap;
}
.nx-pf-nf-link {
    font-size:11px; color:#4F46E5; font-weight:600; text-decoration:none;
    background:#EEF2FF; padding:2px 6px; border-radius:4px;
}

/* Section */
.nx-pf-section { max-width:1000px; margin:0 auto; }
.nx-pf-section-header { margin-bottom:20px; }
.nx-pf-section-header h2 { font-size:16px; font-weight:700; color:#0F172A; margin-bottom:4px; }
.nx-pf-section-header p { font-size:13px; color:#64748B; }
.nx-pf-section-block { margin-top:20px; }
.nx-pf-section-block h3 { font-size:13px; font-weight:600; color:#374151; margin-bottom:12px; }
.nx-pf-block-title { font-size:13px; font-weight:600; color:#374151; }

/* Grids */
.nx-pf-grid-2 { display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; }
.nx-pf-field--full { grid-column:1/-1; }

/* Fields */
.nx-field { display:flex; flex-direction:column; gap:6px; }
.nx-field label { font-size:13px; font-weight:500; color:#374151; }
.nx-field input, .nx-field select, .nx-field textarea {
    padding:8px 12px; border:1px solid #E2E8F0; border-radius:8px;
    font-size:13px; color:#0F172A; background:#fff;
    transition:border-color .15s;
}
.nx-field input:focus, .nx-field select:focus, .nx-field textarea:focus {
    outline:none; border-color:#818CF8;
}
.nx-field-error { color:#DC2626; font-size:12px; }
.nx-required { color:#DC2626; }

/* Product search */
.nx-pf-product-search { position:relative; margin-bottom:16px; }
.nx-pf-product-search-wrap {
    display:flex; align-items:center; gap:8px; padding:10px 14px;
    border:1px solid #E2E8F0; border-radius:10px; background:#fff;
}
.nx-pf-product-search-wrap svg { color:#94A3B8; flex-shrink:0; }
.nx-pf-product-search-wrap input {
    border:none; outline:none; font-size:13px; flex:1;
    color:#0F172A; background:transparent; padding:0;
}
.nx-pf-product-dropdown {
    position:absolute; top:100%; left:0; right:0; z-index:50;
    background:#fff; border:1px solid #E2E8F0; border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,0.1); overflow:hidden; margin-top:4px;
    max-height:400px; overflow-y:auto;
}
.nx-pf-product-item {
    display:flex; align-items:center; justify-content:space-between; width:100%;
    padding:10px 14px; border:none; background:none; cursor:pointer;
    text-align:left; transition:background .1s; font-size:13px;
    border-bottom:1px solid #F1F5F9;
}
.nx-pf-product-item:last-child { border-bottom:none; }
.nx-pf-product-item:hover { background:#F8FAFC; }
.nx-pf-product-item-info { display:flex; flex-direction:column; gap:2px; flex:1; }
.nx-pf-product-name { font-weight:600; color:#0F172A; display:block; }
.nx-pf-product-code { font-size:11px; color:#94A3B8; }
.nx-pf-product-price { font-weight:700; color:#4F46E5; white-space:nowrap; margin-left:12px; }

/* Items table */
.nx-pf-items-table {
    overflow-x:auto; border:1px solid #E2E8F0; border-radius:10px;
    background:#fff; margin-top:16px;
}
.nx-pf-items-table table { width:100%; border-collapse:collapse; font-size:12px; }
.nx-pf-items-table th {
    background:#F8FAFC; color:#64748B; font-weight:600;
    padding:10px 8px; text-align:left; border-bottom:1px solid #E2E8F0;
    white-space:nowrap; position:sticky; top:0; z-index:1;
}
.nx-pf-items-table td {
    padding:8px; border-bottom:1px solid #F1F5F9;
    vertical-align:middle; background:#fff;
}
.nx-pf-items-table tr:last-child td { border-bottom:none; }
.nx-pf-item-name { font-weight:500; color:#0F172A; }
.nx-pf-item-sku { font-size:11px; color:#94A3B8; margin-top:2px; }
.nx-pf-item-total { font-weight:600; color:#0F172A; white-space:nowrap; }
.nx-pf-cell-input {
    border:1px solid #E2E8F0; border-radius:6px; padding:6px 8px;
    font-size:12px; color:#0F172A; width:100%; background:#fff;
    outline:none; transition:border-color .15s;
}
.nx-pf-cell-input:focus { border-color:#818CF8; }
.nx-pf-remove-btn {
    display:flex; align-items:center; justify-content:center;
    width:28px; height:28px; border:none; background:none; cursor:pointer;
    color:#94A3B8; border-radius:6px; transition:all .15s;
}
.nx-pf-remove-btn:hover { background:#FEE2E2; color:#EF4444; }
.nx-pf-items-empty {
    text-align:center; padding:40px 20px; color:#94A3B8;
    font-size:13px; background:#FAFAFA; border-radius:8px;
}

/* Totals */
.nx-pf-totals-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start; }
@media(max-width:800px){ .nx-pf-totals-grid { grid-template-columns:1fr; } }
.nx-pf-totals-inputs { }
.nx-pf-totals-summary {
    background:#F8FAFC; border:1px solid #E2E8F0;
    border-radius:10px; padding:16px;
}
.nx-pf-total-row {
    display:flex; justify-content:space-between; align-items:center;
    padding:8px 0; border-bottom:1px solid #F1F5F9;
    font-size:13px; color:#475569;
}
.nx-pf-total-row:last-child { border-bottom:none; }
.nx-pf-total-row--taxes { color:#F59E0B; font-size:12px; }
.nx-pf-total-row--grand {
    font-weight:700; font-size:16px; color:#0F172A;
    border-top:2px solid #E2E8F0; border-bottom:none;
    padding-top:12px; margin-top:4px;
}

/* Timeline */
.nx-pf-timeline { display:flex; flex-direction:column; gap:0; }
.nx-pf-timeline-item {
    display:flex; gap:12px; padding:12px 0;
    border-bottom:1px solid #F1F5F9;
}
.nx-pf-timeline-item:last-child { border-bottom:none; }
.nx-pf-timeline-dot {
    width:8px; height:8px; border-radius:50%; background:#818CF8;
    flex-shrink:0; margin-top:4px;
}
.nx-pf-timeline-content { flex:1; }
.nx-pf-timeline-meta {
    display:flex; justify-content:space-between; align-items:baseline;
    gap:12px; margin-bottom:4px;
}
.nx-pf-timeline-action { font-size:13px; font-weight:500; color:#0F172A; }
.nx-pf-timeline-time { font-size:11px; color:#94A3B8; white-space:nowrap; }
.nx-pf-timeline-user { font-size:11px; color:#64748B; }

/* Checkbox label */
.nx-pf-checkbox-label {
    display:flex; align-items:center; gap:6px;
    font-size:13px; color:#64748B; cursor:pointer;
}

/* ── FISCAL DRAWER ── */
.nx-pf-fiscal-overlay {
    position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:100;
    display:flex; align-items:flex-end; justify-content:flex-end;
}
.nx-pf-fiscal-drawer {
    width:100%; max-width:540px; height:100vh; background:#fff; overflow-y:auto;
    box-shadow:-4px 0 32px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:0;
}
.nx-pf-fiscal-drawer-header {
    display:flex; align-items:flex-start; justify-content:space-between;
    padding:24px; border-bottom:1px solid #E2E8F0;
}
.nx-pf-fiscal-drawer-header h2 { font-size:18px; font-weight:700; color:#0F172A; }
.nx-pf-fiscal-drawer-header p  { font-size:13px; color:#64748B; margin-top:4px; }

/* Fiscal warnings */
.nx-pf-fiscal-warnings {
    padding:16px 24px; background:#FFFBEB; border-bottom:1px solid #FDE68A;
}
.nx-pf-fiscal-warnings-title {
    display:flex; align-items:center; gap:6px;
    font-size:12px; font-weight:700; color:#92400E; margin-bottom:8px;
}
.nx-pf-fiscal-warning-item { font-size:12px; padding:4px 0; color:#92400E; }
.nx-pf-fiscal-warning--error { color:#DC2626; }
.nx-pf-fiscal-warning--warning { color:#D97706; }

/* Fiscal summary */
.nx-pf-fiscal-summary {
    padding:16px 24px; background:#F8FAFC; border-bottom:1px solid #E2E8F0;
}
.nx-pf-fiscal-summary-row {
    display:flex; justify-content:space-between;
    font-size:13px; color:#475569; padding:4px 0;
}

/* Action cards */
.nx-pf-fiscal-actions {
    padding:20px 24px; display:flex; flex-direction:column; gap:12px; flex:1;
}
.nx-pf-fiscal-action-card {
    display:flex; align-items:center; gap:14px; padding:16px;
    border:1.5px solid #E2E8F0; border-radius:12px; background:#fff;
    transition:all .18s; user-select:none;
}
.nx-pf-fiscal-action-card:hover {
    border-color:#818CF8; box-shadow:0 4px 14px rgba(99,102,241,0.1);
}
.nx-pf-fac--disabled { opacity:.5; cursor:not-allowed !important; }
.nx-pf-fac-icon {
    width:44px; height:44px; border-radius:10px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.nx-pf-fac-info { flex:1; }
.nx-pf-fac-title { font-size:13px; font-weight:700; color:#0F172A; }
.nx-pf-fac-desc  { font-size:12px; color:#64748B; margin-top:2px; }
.nx-pf-fac-status { flex-shrink:0; }
.nx-pf-status-pill {
    font-size:10px; font-weight:700; padding:4px 8px; border-radius:6px;
    display:block; text-align:center; line-height:1.4; white-space:nowrap;
}

/* Badge extras */
.nx-so-badge--waiting     { background:#F3E8FF; color:#7C3AED; }
.nx-so-badge--ready       { background:#DBEAFE; color:#1D4ED8; }
.nx-so-badge--transmitted { background:#D1FAE5; color:#065F46; }
.nx-so-badge--rejected    { background:#FEE2E2; color:#991B1B; }

/* Modal close button */
.nx-modal-close {
    width:32px; height:32px; border-radius:8px; border:1px solid #E2E8F0;
    background:#fff; color:#64748B; display:flex; align-items:center;
    justify-content:center; cursor:pointer; transition:all .15s;
}
.nx-modal-close:hover { background:#FEE2E2; border-color:#FECACA; color:#DC2626; }

/* Responsive */
@media(max-width:900px){
    .nx-pf-body { flex-direction:column; }
    .nx-pf-sidenav {
        width:100%; flex-direction:row; overflow-x:auto; height:auto;
        border-right:none; border-bottom:1px solid #E2E8F0; padding:8px;
    }
    .nx-pf-sidebar { display:none; }
    .nx-pf-fiscal-drawer { max-width:100%; }
    .nx-pf-grid-2 { grid-template-columns:1fr; }
}
</style>
@endpush



