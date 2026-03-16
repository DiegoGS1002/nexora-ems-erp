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

<form action="{{route('vehicles.update', $vehicle->id)}}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="model" placeholder="Modelo" required value="{{ $vehicle->model }}">
    <input type="text" name="brand" placeholder="Marca" required value="{{ $vehicle->brand }}">
    <input type="text" name="year" placeholder="Ano" required value="{{ $vehicle->year }}">
    <input type="text" name="plate" placeholder="Placa" required value="{{ $vehicle->plate }}">
    <input type="text" name="color" placeholder="Cor" required value="{{ $vehicle->color }}">
    <input type="text" name="driver" placeholder="Motorista" value="{{ $vehicle->driver }}">

    <button type="submit">Salvar</button>
    <a class="btn-back" href="{{ route('vehicles.index') }}"> Voltar </a>

</form>
