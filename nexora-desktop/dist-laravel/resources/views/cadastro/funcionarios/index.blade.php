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

    @section('title', 'Funcionários')

    @section('content')
        <main class="container-client">
            <div class="titulo">
                <h1>Lista de Funcionários</h1>
                <div class="button-create-client">
                    <button>
                        <a href="{{ route('employees.create') }}">
                            Adicionar Funcionário
                        </a>
                    </button>
                    <button class="download">
                        <a href="{{ route('employees.print') }}">
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
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>CPF</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach($employees as $employee)
                    <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->role }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone_number }}</td>
                            <td>{{ $employee->address }}</td>
                            <td>{{ $employee->identification_number }}</td>
                            <td>
                                <a href="{{ route('employees.edit', $employee) }}" class="edit">Editar</a>

                                <formaction="{{ route('employees.destroy', $employee) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Deseja excluir este funcionário?')" class="delete">
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
