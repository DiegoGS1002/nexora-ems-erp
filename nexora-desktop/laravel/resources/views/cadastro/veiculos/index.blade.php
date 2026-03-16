<style>
    .container-client{
        padding: 20px;
    }
    .button-create-client{
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .button-create-client button{
        padding: 10px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .button-create-client button a{
        text-decoration: none;
        color: white;
    }

    .table-clients{
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-clients th,
    .table-clients td{
        padding: 12px 10px;
        border: 1px solid #ddd;
    }

    .table-clients tr:nth-child(even){
        background-color: #f9f9f9;
    }

    .table-clients tbody tr:hover{
        background-color: #f1f1f1;
    }

    .table-clients thead {
        background-color: #f2f2f2;
    }

    .table-clients th {
        font-weight: bold;
    }

     .download svg{
        vertical-align: middle;
        margin-right: 5px;
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


    h1 {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 30px;
        font-weight: bold;
        color: #000;
    }

</style>

    @extends('layouts.app')

    @section('title', 'Veiculos')

    @section('content')
        <main class="container-client">
            <div class="titulo">
                <h1>Lista de veiculos</h1>
                <div class="button-create-client">
                    <button>
                        <a href="{{ route('vehicles.create') }}">
                            Adicionar veiculo
                        </a>
                    </button>
                    <button class="download">
                        <a href="{{ route('vehicles.print') }}">
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
            <table class="table-clients">
                <thead>
                    <tr class="table-header">
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Ano</th>
                        <th>Placa</th>
                        <th>Cor</th>
                        <th>Motorista</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach($vehicles as $vehicle)
                    <tr>
                            <td>{{ $vehicle->model }}</td>
                            <td>{{ $vehicle->brand }}</td>
                            <td>{{ $vehicle->year }}</td>
                            <td>{{ $vehicle->plate }}</td>
                            <td>{{ $vehicle->color }}</td>
                            <td>{{ $vehicle->driver }}</td>
                            <td>
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="edit">Editar</a>

                                <formaction="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Deseja excluir este veiculo?')" class="delete">
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
