<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorio de Fornecedores</title>
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

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    @include('cadastro.partials.pdf-header', [
        'title'       => 'Relatório de Fornecedores',
        'recordCount' => $suppliers->count(),
        'search'      => $search ?? '',
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 24%;">Fornecedor</th>
                <th style="width: 14%;">CNPJ</th>
                <th style="width: 22%;">E-mail</th>
                <th style="width: 14%;">Telefone</th>
                <th style="width: 20%;">Cidade / Estado</th>
                <th style="width: 6%;" class="center">CEP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>
                        <strong>{{ $supplier->social_name }}</strong>
                        @if($supplier->name)
                            <div>{{ $supplier->name }}</div>
                        @endif
                    </td>
                    <td>{{ $supplier->taxNumber ?: '-' }}</td>
                    <td>{{ $supplier->email ?: '-' }}</td>
                    <td>{{ $supplier->phone_number ?: '-' }}</td>
                    <td>
                        @if($supplier->address_city)
                            {{ $supplier->address_city }}{{ $supplier->address_state ? ' / ' . $supplier->address_state : '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $supplier->address_zip_code ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Nenhum fornecedor encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
