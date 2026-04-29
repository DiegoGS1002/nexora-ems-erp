<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Produtos</h1>
            <p class="nx-page-subtitle">Gerencie o catalogo de produtos da empresa</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('products.create') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Produto
            </a>
            <a
                href="{{ route('products.print', [
                    'search' => $search,
                    'supplier_id' => $supplierId,
                    'unit_of_measure' => $unitOfMeasure,
                    'category' => $category,
                    'expiration_date' => $expirationDate,
                ]) }}"
                class="nx-btn nx-btn-outline"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar
            </a>
        </div>
    </div>

    <div class="nx-filters-bar">
        <div class="nx-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" class="nx-search" placeholder="Pesquisar produto...">
        </div>

        <select wire:model.live="supplierId" class="nx-filter-select">
            <option value="">Todos os fornecedores</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->social_name }}</option>
            @endforeach
        </select>

        <select wire:model.live="unitOfMeasure" class="nx-filter-select">
            <option value="">Todas as unidades</option>
            <option value="UN">UN — Unidade</option>
            <option value="CX">CX — Caixa</option>
            <option value="KG">KG — Quilograma</option>
            <option value="LT">LT — Litro</option>
            <option value="MT">MT — Metro</option>
            <option value="PC">PC — Peça</option>
            <option value="DZ">DZ — Dúzia</option>
        </select>

        <select wire:model.live="category" class="nx-filter-select">
            <option value="">Todas as categorias</option>
            <option value="informatica">Informática</option>
            <option value="moveis">Móveis</option>
            <option value="eletronicos">Eletrônicos</option>
            <option value="alimentos">Alimentos</option>
            <option value="vestuario">Vestuário</option>
            <option value="ferramentas">Ferramentas</option>
            <option value="outro">Outro</option>
        </select>

        <select wire:model.live="expirationDate" class="nx-filter-select">
            <option value="">Validade</option>
            <option value="expired">Vencidos</option>
            <option value="valid">Validos</option>
            <option value="na">Sem validade</option>
        </select>

        <div class="nx-filter-actions">
            <button type="button" wire:click="clearFilters" class="nx-btn nx-btn-outline nx-btn-sm">Limpar</button>
        </div>
    </div>

    <div class="nx-card">
        @if($products->isEmpty())
            @include('partials.empty-state', [
                'title' => 'Nenhum produto encontrado',
                'description' => 'Adicione produtos ao catalogo ou ajuste os filtros de busca.',
                'actionLabel' => 'Novo Produto',
                'actionRoute' => route('products.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Descricao</th>
                        <th class="nx-th-center">Estoque</th>
                        <th>Fornecedor</th>
                        <th>Validade</th>
                        <th>Codigo EAN</th>
                        <th>Categoria</th>
                        <th class="nx-th-right">Preco</th>
                        <th class="nx-th-center">Imagem</th>
                        <th class="nx-th-actions">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>
                            @if($product->description)
                                <span title="{{ $product->description }}" style="display:block;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->description }}</span>
                            @else
                                <span class="nx-td-muted">-</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            <span class="nx-badge {{ $product->stock > 0 ? 'nx-badge-success' : 'nx-badge-danger' }}">{{ $product->stock }}</span>
                        </td>
                        <td>
                            @if($product->suppliers->isNotEmpty())
                                {{ $product->suppliers->pluck('social_name')->join(', ') }}
                            @else
                                <span style="color:#94A3B8;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($product->expiration_date)
                                @php $exp = \Carbon\Carbon::parse($product->expiration_date); @endphp
                                <span class="nx-badge {{ $exp->isPast() ? 'nx-badge-danger' : 'nx-badge-success' }}">{{ $exp->format('d/m/Y') }}</span>
                            @else
                                <span style="color:#94A3B8;font-size:12px;">N/A</span>
                            @endif
                        </td>
                        <td><span style="font-family:monospace;font-size:12px;">{{ $product->ean ?: '-' }}</span></td>
                        <td>{{ $product->category ? ucfirst($product->category) : '-' }}</td>
                        <td class="nx-td-right"><strong>R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</strong></td>
                        <td class="nx-td-center">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #E2E8F0;">
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('products.edit', $product) }}" class="nx-action-btn nx-action-edit" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <button
                                type="button"
                                class="nx-action-btn nx-action-delete"
                                title="Excluir"
                                wire:click="deleteProduct('{{ $product->id }}')"
                                wire:confirm="Deseja excluir o produto {{ $product->name }}?"
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
            <span class="nx-table-count">{{ $products->total() }} {{ $products->total() === 1 ? 'produto' : 'produtos' }}</span>
            <div class="nx-pagination">{{ $products->links() }}</div>
        </div>
        @endif
    </div>

</div>

