<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorio de Clientes</title>
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
        'title'       => 'Relatório de Clientes',
        'recordCount' => $clients->count(),
        'search'      => $search ?? '',
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 24%;">Cliente</th>
                <th style="width: 10%;" class="center">Tipo</th>
                <th style="width: 14%;">CNPJ / CPF</th>
                <th style="width: 22%;">E-mail</th>
                <th style="width: 12%;">Telefone</th>
                <th style="width: 18%;">Cidade / Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>
                        <strong>{{ $client->name }}</strong>
                        @if($client->social_name)
                            <div>{{ $client->social_name }}</div>
                        @endif
                    </td>
                    <td class="center">{{ $client->tipo_pessoa?->label() ?? '-' }}</td>
                    <td>{{ $client->taxNumber ?: '-' }}</td>
                    <td>{{ $client->email ?: '-' }}</td>
                    <td>{{ $client->phone_number ?: '-' }}</td>
                    <td>
                        @if($client->address_city)
                            {{ $client->address_city }}{{ $client->address_state ? ' / ' . $client->address_state : '' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Nenhum cliente encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
