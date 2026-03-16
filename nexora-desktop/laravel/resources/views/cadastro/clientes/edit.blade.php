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

<form action="{{route('clients.update')}}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone_number" placeholder="Telefone" required>
    <input type="text" name="address" placeholder="Endereço" required>
    <input type="text" name="social_name" placeholder="Razão Social">
    <input type="text" name="taxNumber" placeholder="CNPJ" required>

    <button type="submit">Salvar</button>
    <a class="btn-back" href="{{ route('clients.index') }}"> Voltar </a>

</form>
