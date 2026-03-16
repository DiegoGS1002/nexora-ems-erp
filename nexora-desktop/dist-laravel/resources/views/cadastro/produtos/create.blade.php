@extends('layouts.app')

<link rel="stylesheet" href="{{ app()->environment() === 'production' ? secure_asset('css/forms.css') : asset('css/forms.css') }}">

@section('content')
@section('title', 'Adicionar Produto')
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>Adicionar Produto</h1>

    <form action="{{ route('products.store') }}" method="POST" class="supplier-form" id="productForm" enctype="multipart/form-data">
        @csrf

        {{-- Informações básicas --}}
        <div class="form-section">
            <h3>Informações Básicas</h3>
            <div class="grid grid-2">
                <div>
                    <label>Nome do Produto:</label>
                    <input type="text" name="name" placeholder="Insira o nome do produto"
                    value="{{ old('name') }}" required>
                </div>

                <div>
                    <label>Código de Barras (GTIN/EAN):</label>
                    <input type="text" name="ean" placeholder="Insira o código de barras"
                    value="{{ old('ean') }}" required>
                </div>
            </div>
        </div>

        {{-- Descrição --}}
        <div class="form-section">
            <h3>Descrição</h3>
            <div>
                <label>Descrição:</label>
                <input type="text" name="description" placeholder="Descreva brevemente o produto"
                value="{{ old('description') }}">
            </div>
        </div>

        {{-- Seleção --}}
        <div class="form-section">
            <h3>Seleção</h3>
            <div class="grid grid-3">
                <div>
                    <label>Unidade de Medida:</label>
                    <select name="unit_of_measure" required class="select">
                        <option value="">Selecione a unidade</option>
                        <option value="unidade" {{ old('unit_of_measure') == 'unidade' ? 'selected' : '' }}>Unidade</option>
                        <option value="kg" {{ old('unit_of_measure') == 'kg' ? 'selected' : '' }}>Kg</option>
                        <option value="litro" {{ old('unit_of_measure') == 'litro' ? 'selected' : '' }}>Litro</option>
                        <option value="metro" {{ old('unit_of_measure') == 'metro' ? 'selected' : '' }}>Metro</option>
                    </select>
                </div>
                <div>
                    <label>Categoria:</label>
                    <select name="category" class="select">
                        <option value="">Todas as categorias</option>
                        <option value="eletronico" {{ old('category') == 'eletronico' ? 'selected' : '' }}>Eletrônico</option>
                        <option value="alimentos" {{ old('category') == 'alimentos' ? 'selected' : '' }}>Alimentos</option>
                        <option value="vestuario" {{ old('category') == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                        <option value="outro" {{ old('category') == 'outro' ? 'selected' : '' }}>Outro</option>
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
                    <input type="number" step="0.01" name="sale_price"
                    placeholder="Preço de Venda" value="{{ old('sale_price') }}">
                </div>
                <div>
                    <label>Estoque</label>
                    <input type="number" name="stock" placeholder="Quantidade disponível"
                    value="{{ old('stock') }}" required>
                </div>
                <div>
                    <label>Data de Validade:</label>
                    <input type="date" name="expiration_date" placeholder="Data de Validade"
                    value="{{ old('expiration_date') }}">
                    <small style="color:#666;">
                        Deixe em branco para produtos sem validade
                    </small>
                </div>
            </div>
        </div>
        {{-- Imagem --}}
            <div class="grid grid-1">
                <h2>Imagem do produto</h2>
                <label>Imagem</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

        <button type="submit" class="btn-save">Salvar Produto</button>
        <a href="{{ route('products.index') }}" class="btn-back"> Voltar </a>
    </form>
    <script>
        const form = document.getElementById('productForm');
        const linkSupplier = document.getElementById('linkSupplier');

        const requiredFields = form.querySelectorAll(
            'input[required], select[required]'
        );

        function checkForm() {
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                }
            });

            linkSupplier.style.display = valid ? 'inline-block' : 'none';
        }

        requiredFields.forEach(field => {
            field.addEventListener('input', checkForm);
            field.addEventListener('change', checkForm);
        });
    </script>'

@endsection
