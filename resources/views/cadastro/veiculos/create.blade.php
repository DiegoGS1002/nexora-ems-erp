@extends('layouts.app')
@section('title', 'Novo Veículo')
@section('content')

<div class="nx-form-page">

    <div class="nx-form-header">
        <a href="{{ route('vehicles.index') }}" class="nx-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Veículos
        </a>
        <h1 class="nx-form-title">Cadastrar Veículo</h1>
        <p class="nx-form-subtitle">Preencha os dados do novo veículo</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('vehicles.store') }}" method="POST">
        @csrf

        <div class="nx-form-card">
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Identificação</h3>
                </div>
                <div class="grid grid-2" style="margin-bottom:16px;">
                    <div class="nx-field">
                        <label>Modelo</label>
                        <input type="text" name="model" value="{{ old('model') }}" placeholder="Ex: Sprinter, HB20..." required>
                    </div>
                    <div class="nx-field">
                        <label>Marca</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Ex: Mercedes, Hyundai..." required>
                    </div>
                </div>
                <div class="grid grid-3">
                    <div class="nx-field">
                        <label>Ano</label>
                        <input type="text" name="year" value="{{ old('year') }}" placeholder="Ex: 2023" required>
                    </div>
                    <div class="nx-field">
                        <label>Placa</label>
                        <input type="text" name="plate" value="{{ old('plate') }}" placeholder="ABC-1234" required>
                    </div>
                    <div class="nx-field">
                        <label>Cor</label>
                        <input type="text" name="color" value="{{ old('color') }}" placeholder="Ex: Branco" required>
                    </div>
                </div>
            </div>

            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Motorista Responsável</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Nome do Motorista</label>
                        <input type="text" name="driver" value="{{ old('driver') }}" placeholder="Nome do motorista (opcional)">
                    </div>
                </div>
            </div>
        </div>

        <div class="nx-form-footer">
            <a href="{{ route('vehicles.index') }}" class="nx-btn nx-btn-ghost">Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Salvar Veículo
            </button>
        </div>
    </form>
</div>
@endsection
