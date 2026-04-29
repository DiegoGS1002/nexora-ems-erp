<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            margin-bottom: 16px;
        }

        .title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 4px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f3f4f6;
            text-align: left;
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    @include('cadastro.partials.pdf-header', [
        'title'       => 'Relatório de Produtos',
        'recordCount' => $products->count(),
        'search'      => '',
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Produto</th>
                <th style="width: 22%;">Descricao</th>
                <th style="width: 8%;" class="center">Estoque</th>
                <th style="width: 16%;">Fornecedor</th>
                <th style="width: 9%;" class="center">Validade</th>
                <th style="width: 10%;">Codigo EAN</th>
                <th style="width: 8%;">Categoria</th>
                <th style="width: 10%;" class="right">Preco</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description ?: '-' }}</td>
                    <td class="center">{{ $product->stock }}</td>
                    <td>
                        @if($product->suppliers->isNotEmpty())
                            {{ $product->suppliers->pluck('social_name')->join(', ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $product->expiration_date?->format('d/m/Y') ?? 'N/A' }}</td>
                    <td>{{ $product->ean ?: '-' }}</td>
                    <td>{{ $product->category ? ucfirst($product->category) : '-' }}</td>
                    <td class="right">R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Nenhum produto encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
