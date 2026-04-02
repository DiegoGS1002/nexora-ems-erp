@extends('layouts.app')
@section('title', 'Nova Função')
@section('content')

<div class="nx-form-page">

    <div class="nx-form-header">
        <a href="{{ route('role.index') }}" class="nx-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Funções
        </a>
        <h1 class="nx-form-title">Cadastrar Função</h1>
        <p class="nx-form-subtitle">Defina um novo cargo ou função</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('role.store') }}" method="POST">
        @csrf

        <div class="nx-form-card">
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Dados da Função</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Nome</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Analista, Gerente, Motorista..." required>
                    </div>
                    <div class="nx-field">
                        <label>Descrição</label>
                        <input type="text" name="description" value="{{ old('description') }}" placeholder="Breve descrição das responsabilidades">
                    </div>
                </div>
            </div>
        </div>

        <div class="nx-form-footer">
            <a href="{{ route('role.index') }}" class="nx-btn nx-btn-ghost">Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Salvar Função
            </button>
        </div>
    </form>
</div>
@endsection
