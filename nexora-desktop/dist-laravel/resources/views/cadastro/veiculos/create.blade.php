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

<form action="{{route('vehicles.store')}}" method="POST" class="supplier-form">
    @csrf
    <div class="form-section">
        <h1>Cadastrar veiculo</h1>
        <div class="grid grid-2">
            <div>
                <label>Modelo</label>
                <input type="text" name="model" placeholder="Digite o modelo do veiculo" required>
            </div>
            <div>
                <label>Marca</label>
                <input type="text" name="brand" placeholder="Digite a marca do veiculo" required>
            </div>
        </div>
        <div class="grid grid-2">
            <div>
                <label>Ano</label>
                <input type="text" name="year" placeholder="Ano do veiculo" required>
            </div>
            <div>
                <label>Placa</label>
                <input type="text" name="plate" placeholder="Placa do veiculo" required>
            </div>
        </div>
        <div class="grid grid-1">
            <label>Cor</label>
            <input type="text" name="color" placeholder="Digite a cor do veiculo" required>
        </div>
        <div class="grid grid-1">
            <label>Motorista</label>
            <input type="text" name="driver" placeholder="Nome do motorista">

        </div>
    </div>

    <button type="submit" class="btn-save">Salvar Veiculo</button>
    <a class="btn-back" href="{{ route('vehicles.index') }}"> Voltar </a>
</form>
