@extends('layouts.app')
@section('title', 'Editar Fornecedor')

<link rel="stylesheet" href="{{ app()->environment() === 'production' ? secure_asset('css/forms.css') : asset('css/forms.css') }}">

@section('content')

@if ($errors->any())
    <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px;">
        <ul style="margin:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1><strong>Editar Fornecedor</strong></h1>

<form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="supplier-form">
    @csrf
    @method('PUT')

    {{-- INFORMAÇÕES --}}
    <div class="form-section">
        <h3>Informações</h3>

        <div class="grid grid-2">
            <div>
                <label>Razão Social</label>
                <input type="text"
                       name="social_name"
                       value="{{ old('social_name', $supplier->social_name) }}"
                       required>
            </div>

            <div>
                <label>CNPJ</label>
                <input type="text"
                       name="taxNumber"
                       value="{{ old('taxNumber', $supplier->taxNumber) }}"
                       required>
            </div>
        </div>
    </div>

    {{-- ENDEREÇO --}}
    <div class="form-section">
        <h3>Endereço</h3>

        <div class="grid grid-5">
            <input type="text" name="address_street"
                   value="{{ old('address_street', $supplier->address_street) }}"
                   placeholder="Rua">

            <input type="text" name="address_number"
                   value="{{ old('address_number', $supplier->address_number) }}"
                   placeholder="Número">

            <input type="text" name="address_district"
                   value="{{ old('address_district', $supplier->address_district) }}"
                   placeholder="Bairro">

            <input type="text" name="address_city"
                   value="{{ old('address_city', $supplier->address_city) }}"
                   placeholder="Cidade">

            <input type="text" name="address_state"
                   value="{{ old('address_state', $supplier->address_state) }}"
                   maxlength="2"
                   placeholder="UF">

            <input type="text" name="address_complement"
                   value="{{ old('address_complement', $supplier->address_complement) }}"
                   placeholder="Complemento">

            <input type="text" name="address_zip_code"
                   value="{{ old('address_zip_code', $supplier->address_zip_code) }}"
                   placeholder="00000-000">
        </div>
    </div>

    {{-- CONTATOS --}}
    <div class="form-section">
        <h3>Contatos</h3>

        <div class="grid grid-2">
            <input type="text" name="phone_number"
                   value="{{ old('phone_number', $supplier->phone_number) }}"
                   placeholder="Telefone">

            <input type="email" name="email"
                   value="{{ old('email', $supplier->email) }}"
                   placeholder="Email">
        </div>
    </div>

    <button type="submit" class="btn-save">
        Atualizar Fornecedor
    </button>
    <a class="btn-back" href="{{ route('suppliers.index') }}"> Voltar </a>

</form>

@endsection
