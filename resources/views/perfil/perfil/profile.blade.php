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
