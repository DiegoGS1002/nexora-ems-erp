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

<form action="{{route('employees.update', $employee->id)}}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" placeholder="Nome" required value="{{ $employee->name }}">
    <input type="email" name="email" placeholder="Email" required value="{{ $employee->email }}">
    <input type="text" name="phone_number" placeholder="Telefone" required value="{{ $employee->phone_number }}">
    <input type="text" name="address" placeholder="Endereço" required value="{{ $employee->address }}">
    <input type="text" name="identification_number" placeholder="CPF" required value="{{ $employee->identification_number }}">
    <div class="grid grid-3">
    <div> <label>Funções</label>
            <select name="unit_of_measure" required></select>
    </div>

    <button type="submit">Salvar</button>
    <a class="btn-back" href="{{ route('employees.index') }}"> Voltar </a>

</form>
