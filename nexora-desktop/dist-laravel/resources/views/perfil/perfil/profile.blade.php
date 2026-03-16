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
        padding: 6px 12px;
        background-color: #4CAF50;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        align-items: center;
        border: 1px solid #317033;
        text-align: center;
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

    @section('title', 'Configuração do Perfil')

    @section('content')
        <main class="container-client">
            <div class="titulo">
                <h1>Perfil</h1>
            </div>
            <form action="{{route('profile.store')}}" method="POST" class="supplier-form">
                @csrf
                <div class="form-section">
                    <div class="grid grid-2">
                        <div>
                            <label>Nome</label>
                            <input type="text" name="name" placeholder="Digite o nome do usuário" required>
                        </div>
                        <div>
                            <label>CNPJ/CPF</label>
                            <input type="text" name="taxNumber" placeholder="00.000.000/0000-00" required>
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div>
                            <label>E-mail</label>
                            <input type="email" name="email" placeholder="email@exemplo.com" required>
                        </div>
                        <div>
                            <label>Telefone</label>
                            <input type="text" name="phone_number" placeholder="(00) 00000-0000)" required>
                        </div>
                    </div>
                    <div class="grid grid-1">
                        <label>Endereço</label>
                        <input type="text" name="address" placeholder="Digite o endereço completo do usuário" required>
                    </div>
                    <div class="grid grid-1">
                        <label>Razão social</label>
                        <input type="text" name="social_name" placeholder="Razão Social se aplicado">
                    </div>
                </div>

                <button type="submit" class="btn-save"> Salvar </button>
                <a class="btn-back" href="{{ route('home.index') }}"> Voltar </a>
            </form>
        </main>
    @endsection
