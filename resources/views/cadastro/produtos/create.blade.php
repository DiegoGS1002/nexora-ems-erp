@extends('layouts.app')
@section('title', 'Novo Produto')
@section('content')

<div class="nx-form-page" style="max-width:960px;">

    <div class="nx-form-header">
        <a href="{{ route('products.index') }}" class="nx-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Produtos
        </a>
        <h1 class="nx-form-title">Adicionar Produto</h1>
        <p class="nx-form-subtitle">Preencha os dados do novo produto</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" id="productForm" enctype="multipart/form-data">
        @csrf

        <div class="nx-form-card">

            {{-- Informações Básicas --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Informações Básicas</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Nome do Produto</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nome do produto" required>
                    </div>
                    <div class="nx-field">
                        <label>Código de Barras (GTIN/EAN)</label>
                        <input type="text" name="ean" value="{{ old('ean') }}" placeholder="Ex: 7891234567890" required>
                    </div>
                </div>
            </div>

            {{-- Descrição --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Descrição</h3>
                </div>
                <div class="nx-field">
                    <label>Descrição do Produto</label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Descreva brevemente o produto">
                </div>
            </div>

            {{-- Classificação --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Classificação</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label>Unidade de Medida</label>
                        <select name="unit_of_measure" required>
                            <option value="">Selecione a unidade</option>
                            <option value="unidade" {{ old('unit_of_measure') == 'unidade' ? 'selected' : '' }}>Unidade</option>
                            <option value="kg"      {{ old('unit_of_measure') == 'kg'      ? 'selected' : '' }}>Kg</option>
                            <option value="litro"   {{ old('unit_of_measure') == 'litro'   ? 'selected' : '' }}>Litro</option>
                            <option value="metro"   {{ old('unit_of_measure') == 'metro'   ? 'selected' : '' }}>Metro</option>
                        </select>
                    </div>
                    <div class="nx-field">
                        <label>Categoria</label>
                        <select name="category">
                            <option value="">Selecione a categoria</option>
                            <option value="eletronico" {{ old('category') == 'eletronico' ? 'selected' : '' }}>Eletrônico</option>
                            <option value="alimentos"  {{ old('category') == 'alimentos'  ? 'selected' : '' }}>Alimentos</option>
                            <option value="vestuario"  {{ old('category') == 'vestuario'  ? 'selected' : '' }}>Vestuário</option>
                            <option value="outro"      {{ old('category') == 'outro'      ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Estoque & Preço --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Estoque & Precificação</h3>
                </div>
                <div class="grid grid-3">
                    <div class="nx-field">
                        <label>Preço de Venda</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" placeholder="0,00">
                    </div>
                    <div class="nx-field">
                        <label>Estoque Inicial</label>
                        <input type="number" name="stock" value="{{ old('stock') }}" placeholder="0" required>
                    </div>
                    <div class="nx-field">
                        <label>Data de Validade</label>
                        <input type="date" name="expiration_date" value="{{ old('expiration_date') }}">
                        <small>Deixe em branco para produtos sem validade</small>
                    </div>
                </div>
            </div>

            {{-- Imagem --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <h3 class="nx-form-section-title">Imagem do Produto</h3>
                </div>
                <div class="nx-field">
                    <label>Foto do Produto</label>
                    <input type="file" name="image" accept="image/*" required>
                    <small>Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 2MB.</small>
                </div>
            </div>
        </div>

        <div class="nx-form-footer">
            <a href="{{ route('products.index') }}" class="nx-btn nx-btn-ghost">Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Salvar Produto
            </button>
        </div>
    </form>
</div>
@endsection
