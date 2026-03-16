@extends('layouts.app')

<style>

.container-supplier{
    padding: 20px;
}

.button-create-supplier{
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.button-create-supplier button{
    padding: 6px 12px;
    background-color: #4CAF50;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    align-items: center;
    border: 1px solid #317033;
    text-align: center;
}

.button-create-supplier button a{
    text-decoration: none;
    color: white;
}

.table-supplier{
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table-supplier th, .table-supplier td{
    padding: 12px 10px;
    border: 1px solid #ddd;
}

.table-supplier tr:nth-child(even){
    background-color: #f9f9f9;
}

.table-supplier tbody tr:hover{
    background-color: #f1f1f1;
}

.table-supplier thead {
    background-color: #f2f2f2;
}

.table-supplier th {
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
    border: none; color: blue;
    cursor: pointer;
    font-size: 1em;
    text-decoration: underline;
    padding: 0;
    font-family: inherit;
}

h1 {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 30px;
    font-weight: bold; color: #000;
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

</style>

@section('title', 'Fornecedores')

@section('content')
    @if($suppliers->isEmpty())
        <tr>
            <td colspan="9">Nenhum fornecedor cadastrado.</td>
        </tr>
    @endif
        <main class="container-supplier">
            <div class="titulo">
                <h1>Lista de Fornecedores</h1>
                <div class="button-create-supplier">
                    <button>
                        <a href="{{ route('suppliers.create') }}">
                            Adicionar Fornecedor
                        </a>
                    </button>
                    <button class="download">
                        <a href="{{ route('suppliers.print') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Baixar Lista
                        </a>
                    </button>
                </div>
            </div>
            <div class="search-filter">
                <input type="text" name="search" placeholder="Pesquisar produto..." value="{{ request('search') }}" class="search-bar">
            </div>
            <table class="table-supplier">
                <thead>
                    <tr class="table-header">
                        <th>Razão Social</th>
                        <th>CNPJ</th>
                        <th>Cidade</th>
                        <th>CEP</th>
                        <th>Rua</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Contato principal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->social_name}}</td>
                            <td>{{ $supplier->taxNumber }}</td>
                            <td>{{ $supplier->address_city }}</td>
                            <td>{{ $supplier->address_zip_code }}</td>
                            <td>{{ $supplier->address_street }}</td>
                            <td>{{ $supplier->phone_number }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="edit">Editar</a>

                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Deseja excluir este fornecedor?')" class="delete">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </main>
@endsection
