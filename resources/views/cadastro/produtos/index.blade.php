@extends('layouts.app')
@section('title', 'Produtos')
@section('content')

<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Produtos</h1>
            <p class="nx-page-subtitle">Gerencie o catálogo de produtos da empresa</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('products.create') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Produto
            </a>
            <a href="{{ route('products.print') }}" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('products.index') }}" class="nx-filters-bar">
        <div class="nx-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="nx-search" placeholder="Pesquisar produto..." value="{{ request('search') }}">
        </div>

        <select name="supplier_id" class="nx-filter-select">
            <option value="">Todos os fornecedores</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->social_name }}
                </option>
            @endforeach
        </select>

        <select name="unit_of_measure" class="nx-filter-select">
            <option value="">Todas as unidades</option>
            <option value="unidade" {{ request('unit_of_measure') == 'unidade' ? 'selected' : '' }}>Unidade</option>
            <option value="kg"      {{ request('unit_of_measure') == 'kg'      ? 'selected' : '' }}>Kg</option>
            <option value="litro"   {{ request('unit_of_measure') == 'litro'   ? 'selected' : '' }}>Litro</option>
            <option value="metro"   {{ request('unit_of_measure') == 'metro'   ? 'selected' : '' }}>Metro</option>
        </select>

        <select name="category" class="nx-filter-select">
            <option value="">Todas as categorias</option>
            <option value="eletronico" {{ request('category') == 'eletronico' ? 'selected' : '' }}>Eletrônico</option>
            <option value="alimentos"  {{ request('category') == 'alimentos'  ? 'selected' : '' }}>Alimentos</option>
            <option value="vestuario"  {{ request('category') == 'vestuario'  ? 'selected' : '' }}>Vestuário</option>
            <option value="outro"      {{ request('category') == 'outro'      ? 'selected' : '' }}>Outro</option>
        </select>

        <select name="expiration_date" class="nx-filter-select">
            <option value="">Validade</option>
            <option value="expired" {{ request('expiration_date') == 'expired' ? 'selected' : '' }}>Vencidos</option>
            <option value="valid"   {{ request('expiration_date') == 'valid'   ? 'selected' : '' }}>Válidos</option>
            <option value="na"      {{ request('expiration_date') == 'na'      ? 'selected' : '' }}>Sem validade</option>
        </select>

        <div class="nx-filter-actions">
            <button type="submit" class="nx-btn nx-btn-primary nx-btn-sm">Filtrar</button>
            <a href="{{ route('products.index') }}" class="nx-btn nx-btn-outline nx-btn-sm">Limpar</a>
        </div>
    </form>

    <div class="nx-card">
        @if($products->isEmpty())
            @include('partials.empty-state', [
                'title'       => 'Nenhum produto encontrado',
                'description' => 'Adicione produtos ao catálogo ou ajuste os filtros de busca.',
                'actionLabel' => 'Novo Produto',
                'actionRoute' => route('products.create'),
            ])
        @else
        <div class="nx-table-wrap">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Descrição</th>
                        <th class="nx-th-center">Estoque</th>
                        <th>Fornecedor</th>
                        <th>Validade</th>
                        <th>Código EAN</th>
                        <th>Categoria</th>
                        <th class="nx-th-right">Preço</th>
                        <th class="nx-th-center">Imagem</th>
                        <th class="nx-th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>
                            @if($product->description)
                                <span title="{{ $product->description }}" style="display:block;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->description }}</span>
                            @else
                                <span class="nx-td-muted">—</span>
                            @endif
                        </td>
                        <td class="nx-td-center">
                            <span class="nx-badge {{ $product->stock > 0 ? 'nx-badge-success' : 'nx-badge-danger' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            @if($product->suppliers->isNotEmpty())
                                {{ $product->suppliers->pluck('social_name')->join(', ') }}
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($product->expiration_date)
                                @php $exp = \Carbon\Carbon::parse($product->expiration_date); @endphp
                                <span class="nx-badge {{ $exp->isPast() ? 'nx-badge-danger' : 'nx-badge-success' }}">
                                    {{ $exp->format('d/m/Y') }}
                                </span>
                            @else
                                <span style="color:#94A3B8;font-size:12px;">N/A</span>
                            @endif
                        </td>
                        <td><span style="font-family:monospace;font-size:12px;">{{ $product->ean ?: '—' }}</span></td>
                        <td>{{ $product->category ? ucfirst($product->category) : '—' }}</td>
                        <td class="nx-td-right"><strong>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</strong></td>
                        <td class="nx-td-center">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                                     style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #E2E8F0;">
                            @else
                                <span style="color:#CBD5E1;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td class="nx-td-actions">
                            <a href="{{ route('products.edit', $product->id) }}" class="nx-action-btn nx-action-edit" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="nx-action-btn nx-action-delete" title="Excluir"
                                        onclick="return confirm('Deseja excluir o produto {{ addslashes($product->name) }}?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </form>
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
@endsection
