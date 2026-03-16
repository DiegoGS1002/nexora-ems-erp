@extends('layouts.app')

    <link rel="stylesheet" href="{{ app()->environment() === 'production' ? secure_asset('css/forms.css') : asset('css/forms.css') }}">

@if ($errors->any())
    <div class="alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{route('employees.store')}}" method="POST" class="supplier-form">
    @csrf
    <div class="form-section">
        <h1>Cadastrar Funcionários</h1>
        <div class="grid grid-2">
            <div>
                <label>Nome</label>
                <input type="text" name="name" placeholder="Digite o nome do funcionário" required>
            </div>
            <div>
                <label>CPF</label>
                <input type="text" name="identification_number" placeholder="00.000.000/0000-00" required>
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
            <input type="text" name="address" placeholder="Digite o endereço completo do cliente" required>
        </div>
        <div class="grid grid-1">
            <div class="grid grid-3">
                <div>
                    <label>Funções</label>
                    <select name="role" required class="select">
                        <option value="">Selecione a função</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-save">Salvar Funcionário</button>
    <a class="btn-back" href="{{ route('employees.index') }}"> Voltar </a>
</form>
