@extends('layouts.app')
@section('title', 'Adicionar Fornecedor')

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

        <h1><strong>Adicionar Fornecedor</strong></h1>

        <form action="{{ route('suppliers.store') }}" method="POST" class="supplier-form">
        @csrf

        {{-- INFORMAÇÕES --}}
        <div class="form-section">
            <h3>Informações</h3>

            <div class="grid grid-2">
                <div>
                    <label>Razão Social</label>
                    <input type="text" name="social_name" value="{{ old('social_name') }}" placeholder="Nome da empresa" required>
                </div>

                <div>
                    <label>CNPJ</label>
                    <input type="text" name="taxNumber" value="{{ old('taxNumber') }}" placeholder="00.000.000/0000-00" required>
                </div>
            </div>
        </div>

        {{-- ENDEREÇO --}}
        <div class="form-section">
            <h3>Endereço</h3>

            <div class="grid grid-5">
                <div>
                    <label>Rua</label>
                    <input type="text" name="address_street" value="{{ old('address_street') }}" placeholder="Rua">
                </div>

                <div>
                    <label>Número</label>
                    <input type="text" name="address_number" value="{{ old('address_number') }}" placeholder="Número">
                </div>

                <div>
                    <label>Bairro</label>
                    <input type="text" name="address_district" value="{{ old('address_district') }}" placeholder="Bairro">
                </div>

                <div>
                    <label>Cidade</label>
                    <input type="text" name="address_city" value="{{ old('address_city') }}" placeholder="Cidade">
                </div>
                <div>
                    <label>UF</label>
                    <input type="text" name="address_state" value="{{ old('address_state') }}" maxlength="2" placeholder="UF">
                </div>
                <div>
                    <label>Complemento</label>
                    <input type="text" name="address_complement" value="{{ old('address_complement') }}" placeholder="Complemento">
                </div>
                <div>
                    <label>CEP</label>
                    <input type="text" name="address_zip_code" value="{{ old('address_zip_code') }}"  placeholder="00000-000">
                </div>
            </div>
        </div>

        {{-- CONTATOS --}}
        <div class="form-section">
            <h3>Contatos</h3>

            <div class="grid grid-2">
                <div>
                    <label>Telefone</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="(00) 00000-0000">
                </div>

                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="exemplo@fornecedor.com" >
                </div>
            </div>
        </div>

        {{-- RESPONSÁVEL --}}
        <div class="form-section">
            <h3>Responsável</h3>

            <div class="grid grid-1">
                <div>
                    <label>Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nome do contato principal">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save"> Salvar Fornecedor </button>
        <a class="btn-back" href="{{ route('suppliers.index') }}"> Voltar </a>
    </form>

@endsection
