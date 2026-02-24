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

<form action="{{route('clients.store')}}" method="POST" class="supplier-form">
    @csrf
    <div class="form-section">
        <h1>Cadastrar cliente</h1>
        <div class="grid grid-2">
            <div>
                <label>Nome</label>
                <input type="text" name="name" placeholder="Digite o nome do cliente" required>
            </div>
            <div>
                <label>CNPJ</label>
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
            <input type="text" name="address" placeholder="Digite o endereço completo do cliente" required>
        </div>
        <div class="grid grid-1">
            <label>Razão social</label>
            <input type="text" name="social_name" placeholder="Razão Social">
        </div>
    </div>

    <button type="submit" class="btn-save">Salvar Cliente</button>
    <a class="btn-back" href="{{ route('clients.index') }}"> Voltar </a>
</form>
