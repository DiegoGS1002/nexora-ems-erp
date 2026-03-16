<style>
    .container-products{
        padding: 25px;
    }
    .button-create-products{
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }
    .button-create-products button{
        padding: 6px 12px;
        background-color: #4CAF50;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        align-items: center;
        border: 1px solid #317033;
        text-align: center;
    }
    .button-create-products button a{
        text-decoration: none;
        color: white;
    }

    .table-products{
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-products th,
    .table-products td{
        padding: 12px 10px;
        border: 1px solid #ddd;
    }

    .table-products tr:nth-child(even){
        background-color: #f9f9f9;
    }

    .table-products tbody tr:hover{
        background-color: #f1f1f1;
    }

    .table-products thead {
        background-color: #f2f2f2;
    }

    .table-products th {
        font-weight: bold;
    }

    .delete{
        background: none;
        border: none;
        color: red;
        cursor: pointer;
        font-size: 1em;
        text-decoration: underline;
        padding: 0;
        font-family: inherit;
    }

    .edit{
        background: none;
        border: none;
        color: blue;
        cursor: pointer;
        font-size: 1em;
        text-decoration: underline;
        padding: 0;
        font-family: inherit;
    }

    .search-filter{
        display: flex;
        justify-content: end;
        margin-bottom: 20px;
        gap: 10px;
    }
    .search-bar{
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .filter{
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .filtrar, .limpar{
        padding: 10px 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    h1 {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 30px;
        font-weight: bold;
        color: #000;
    }
</style>
@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<main class="container-products">
    <div class="titulo">
        <h1>Lista de Produtos</h1>
        <div class="button-create-products">
            <button><a href="{{ route('products.create') }}">Adicionar Produto</a></button>
            <button class="download">
            <a href="{{ route('products.print') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg> Baixar Lista</a></button>
        </div>
    </div>
    <div class="search-filter">
        <form method="GET" action="{{ route('products.index') }}" class="search-filter">
            <input
                type="text"
                name="search"
                placeholder="Pesquisar produto..."
                value="{{ request('search') }}"
                class="search-bar"
            >

            <select name="supplier_id" class="filter">
                <option value="">Todos os fornecedores</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->social_name }}
                    </option>
                @endforeach
            </select>

            <select name="unit_of_measure" class="filter">
                <option value="">Todas as unidades</option>
                <option value="unidade" {{ request('unit_of_measure') == 'unidade' ? 'selected' : '' }}>Unidade</option>
                <option value="kg" {{ request('unit_of_measure') == 'kg' ? 'selected' : '' }}>Kg</option>
                <option value="litro" {{ request('unit_of_measure') == 'litro' ? 'selected' : '' }}>Litro</option>
                <option value="metro" {{ request('unit_of_measure') == 'metro' ? 'selected' : '' }}>Metro</option>
            </select>

            <select name="category" class="filter">
                <option value="">Todas as categorias</option>
                <option value="eletronico" {{ request('category') == 'eletronico' ? 'selected' : '' }}>Eletrônico</option>
                <option value="alimentos" {{ request('category') == 'alimentos' ? 'selected' : '' }}>Alimentos</option>
                <option value="vestuario" {{ request('category') == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                <option value="outro" {{ request('category') == 'outro' ? 'selected' : '' }}>Outro</option>
            </select>

            <select name="expiration_date" class="filter">
                <option value="">Todos os produtos</option>
                <option value="expired" {{ request('expiration_date') == 'expired' ? 'selected' : '' }}>Vencidos</option>
                <option value="valid" {{ request('expiration_date') == 'valid' ? 'selected' : '' }}>Válidos</option>
                <option value="na" {{ request('expiration_date') == 'na' ? 'selected' : '' }}>Sem validade</option>
            </select>

            <button type="submit" class="filtrar">Filtrar</button>
            <a href="{{ route('products.index') }}" class="limpar">Limpar</a>
        </form>
    </div>
    {{ $products->links() }}
    <table class="table-products">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Estoque</th>
                <th>Fornecedor</th>
                <th>Data de Validade</th>
                <th>Código de Barras</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        @if ($product->suppliers->isNotEmpty())
                            {{ $product->suppliers->pluck('social_name')->join(', ') }}
                        @else
                            <span style="color:#888;">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($product->expiration_date)
                            {{ \Carbon\Carbon::parse($product->expiration_date)->format('d/m/Y') }}
                        @else
                            <span style="color:#888;">Não se aplica</span>
                        @endif
                    </td>
                    <td>{{ $product->ean }}</td>
                    <td>{{ $product->category }}</td>
                    <td>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                    <td>
                        @if ($product->image)
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="Imagem do produto"
                                style="max-width:100px; border-radius:8px;"
                            >
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="edit">Editar</a> |
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                class="delete">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum produto encontrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</main>
@endsection
