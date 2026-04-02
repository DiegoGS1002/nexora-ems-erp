@extends('layouts.app')
@section('title', 'Novo Cliente')
@section('content')

<div class="nx-form-page">

    {{-- Cabeçalho --}}
    <div class="nx-form-header">
        <a href="{{ route('clients.index') }}" class="nx-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Voltar para Clientes
        </a>
        <h1 class="nx-form-title">Cadastrar Cliente</h1>
        <p class="nx-form-subtitle">Preencha os dados do novo cliente</p>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf

        {{-- Card: Informações Pessoais --}}
        <div class="nx-form-card">
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Informações Pessoais</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Nome</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nome completo ou fantasia" required>
                    </div>
                    <div class="nx-field">
                        <label>CNPJ</label>
                        <input type="text" name="taxNumber" value="{{ old('taxNumber') }}" placeholder="00.000.000/0000-00" required>
                    </div>
                </div>
            </div>

            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l.85-.85a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 17z"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Contato</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@exemplo.com" required>
                    </div>
                    <div class="nx-field">
                        <label>Telefone</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="(00) 00000-0000" required>
                    </div>
                </div>
            </div>

            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Endereço & Jurídico</h3>
                </div>
                <div class="grid grid-1" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Endereço</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Rua, número, bairro, cidade - UF" required>
                    </div>
                </div>
                <div class="grid grid-1">
                    <div class="nx-field">
                        <label>Razão Social</label>
                        <input type="text" name="social_name" value="{{ old('social_name') }}" placeholder="Razão Social da empresa">
                    </div>
                </div>
            </div>
        </div>

        {{-- Botões --}}
        <div class="nx-form-footer">
            <a href="{{ route('clients.index') }}" class="nx-btn nx-btn-ghost">Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Salvar Cliente
            </button>
        </div>
    </form>

</div>
@endsection
