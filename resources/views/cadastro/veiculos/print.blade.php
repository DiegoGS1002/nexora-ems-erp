<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorio de Veiculos</title>
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
        'title'       => 'Relatório de Veículos',
        'recordCount' => $vehicles->count(),
        'search'      => $search ?? '',
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Placa</th>
                <th style="width: 20%;">Marca / Modelo</th>
                <th style="width: 8%;" class="center">Ano</th>
                <th style="width: 12%;">Tipo</th>
                <th style="width: 16%;">Combustivel</th>
                <th style="width: 18%;">Motorista</th>
                <th style="width: 8%;" class="center">Status</th>
                <th style="width: 10%;">Renavam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->plate ?: '-' }}</td>
                    <td>{{ trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')) ?: '-' }}</td>
                    <td class="center">{{ $vehicle->manufacturing_year ?? $vehicle->year ?? '-' }}</td>
                    <td>{{ $vehicle->vehicle_type?->label() ?? '-' }}</td>
                    <td>{{ $vehicle->fuel_type?->label() ?? '-' }}</td>
                    <td>{{ $vehicle->responsible_driver ?: '-' }}</td>
                    <td class="center">{{ $vehicle->is_active ? 'Ativo' : 'Inativo' }}</td>
                    <td>{{ $vehicle->renavam ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Nenhum veiculo encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
