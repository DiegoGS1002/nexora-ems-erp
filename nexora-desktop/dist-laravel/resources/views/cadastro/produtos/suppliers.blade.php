@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/forms.css') }}">

@section('content')

<div class="supplier-form">

    {{-- Mensagens --}}
    @if ($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1>Associação de Fornecedor a Produto</h1>

    {{-- 🔹 Detalhes do Produto --}}
    <div class="form-section">
        <h3>Detalhes do Produto</h3>

        <div class="grid grid-2">
            <div>
                <label>Produto</label>
                <input type="text" value="{{ $product->name }}" readonly>
            </div>

            <div>
                <label>EAN</label>
                <input type="text" value="{{ $product->ean }}" readonly>
            </div>
        </div>

        <div>
            <label>Descrição</label>
            <input readonly {{ $product->description }}>
        </div>
    </div>

    {{-- 🔹 Associação --}}
    <div class="form-section">
        <h3>Associar Fornecedor</h3>

        @if ($associatedSuppliers->isEmpty())
            <form
                method="POST"
                action="{{ route('products.suppliers.store', $product->id) }}"
                class="associate"
            >
                @csrf

                <label>Fornecedor</label>
                <select name="supplier_id" required>
                    <option value="">Selecione um fornecedor</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->social_name }} - {{ $supplier->taxNumber }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-save">
                    Associar Fornecedor
                </button>
            </form>
        @else
            <p style="color:#555;">
                Este produto já possui um fornecedor associado.
            </p>
        @endif
    </div>

    {{-- 🔹 Fornecedor Associado --}}
    <div class="form-section">
        <h3>Fornecedor Associado</h3>

        @if ($associatedSuppliers->isEmpty())
            <p>Nenhum fornecedor associado.</p>
        @else
            <div class="associated-header grid grid-3">
                <strong>Fornecedor</strong>
                <strong>CNPJ</strong>
                <strong>Ação</strong>
            </div>

            @foreach ($associatedSuppliers as $supplier)
                <div class="associated-item grid grid-3">
                    <div>{{ $supplier->social_name }}</div>
                    <div>{{ $supplier->taxNumber }}</div>

                    <form
                        method="POST"
                        action="{{ route('products.suppliers.destroy', [$product->id, $supplier->id]) }}"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn-remove">
                            Desassociar
                        </button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>

    {{-- 🔹 Ações --}}
    <div class="actions">
        <a href="{{ route('products.index') }}" class="btn-back">
            ← Voltar
        </a>
    </div>

</div>

@endsection
