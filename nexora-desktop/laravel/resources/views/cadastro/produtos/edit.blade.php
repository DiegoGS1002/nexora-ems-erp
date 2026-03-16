@extends('layouts.app')

<link rel="stylesheet" href="{{ app()->environment() === 'production' ? secure_asset('css/forms.css') : asset('css/forms.css') }}">

@section('content')
@section('title', 'Editar Produto')

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>Editar Produto</h1>
    <form action="{{ route('products.update', $product->id) }}" method="POST" class="supplier-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Informações básicas --}}
        <div class="form-section">
            <h3>Informações Básicas</h3>
            <div class="grid grid-2">
                <div>
                    <label>Nome do Produto:</label>
                    <input type="text"
                        name="name" value="{{ old('name', $product->name) }}" required>
                </div>

                {{-- EAN --}}
                <div>
                    <label>Código de Barras (GTIN/EAN):</label>
                    <input type="text" name="ean" value="{{ old('ean', $product->ean) }}" required>
                </div>
            </div>
        </div>

        {{-- Descrição --}}
        <div class="form-section">
            <h3>Descrição</h3>
            <div>
                <label>Descrição:</label>
               <textarea name="description" rows="3" required>
                    {{ old('description', $product->description) }}
                </textarea>
            </div>
        </div>

        {{-- Seleção --}}
        <div class="form-section">
            <h3>Seleção</h3>
            <div class="grid grid-3">
                <div>
                    <label>Unidade de Medida:</label>
                    <select name="unit_of_measure" required>
                        <option value="">Selecione a unidade</option>
                        <option value="unidade"
                            {{ old('unit_of_measure', $product->unit_of_measure) == 'unidade' ? 'selected' : '' }}>
                            Unidade
                        </option>
                        <option value="kg"
                            {{ old('unit_of_measure', $product->unit_of_measure) == 'kg' ? 'selected' : '' }}>
                            Kg
                        </option>
                        <option value="litro"
                            {{ old('unit_of_measure', $product->unit_of_measure) == 'litro' ? 'selected' : '' }}>
                            Litro
                        </option>
                        <option value="metro"
                            {{ old('unit_of_measure', $product->unit_of_measure) == 'metro' ? 'selected' : '' }}>
                            Metro
                        </option>
                    </select>
                </div>

                <div>
                    <label>Categoria:</label>
                    <select name="category" required>
                        <option value="eletronico"
                            {{ old('category', $product->category) == 'eletronico' ? 'selected' : '' }}>
                            Eletrônico
                        </option>
                        <option value="alimentos"
                            {{ old('category', $product->category) == 'alimentos' ? 'selected' : '' }}>
                            Alimentos
                        </option>
                        <option value="vestuario"
                            {{ old('category', $product->category) == 'vestuario' ? 'selected' : '' }}>
                            Vestuário
                        </option>
                        <option value="outro"
                            {{ old('category', $product->category) == 'outro' ? 'selected' : '' }}>
                            Outro
                        </option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Outros --}}
        <div class="form-section">
            <h3>Outros</h3>
            <div class="grid grid-3">
                <div>
                    <label>Preço de Venda:</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" required>
                </div>

                <div>
                    <label>Estoque</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required>
                </div>

                {{-- Data de validade --}}
                <div>
                    <label>Data de Validade</label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date', $product->expiration_date) }}">
                </div>
            </div>
        </div>

        <label>Imagem</label>
        <input type="file" name="image" accept="image/*">

        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" width="120">
        @endif

        {{-- Fornecedores (N:N) --}}
        <div class="mb-4">
            <label class="form-label">Fornecedores</label>
            <select name="suppliers[]"
                    class="form-select"
                    multiple>

                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ $product->suppliers->contains($supplier->id) ? 'selected' : '' }}>
                        {{ $supplier->corporate_name ?? $supplier->social_name }}
                    </option>
                @endforeach

            </select>
            <small class="text-muted">
                Segure CTRL (Windows) para selecionar mais de um
            </small>
        </div>

        {{-- Botões --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-save">
                Salvar Alterações
            </button>

            <a href="{{ route('products.index') }}" class="btn btn-back">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
