<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorio de Funcionarios</title>
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
        'title'       => 'Relatório de Funcionários',
        'recordCount' => $employees->count(),
        'search'      => $search ?? '',
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Nome</th>
                <th style="width: 16%;">Funcao</th>
                <th style="width: 22%;">E-mail</th>
                <th style="width: 14%;">Telefone</th>
                <th style="width: 14%;">Endereco</th>
                <th style="width: 12%;" class="center">CPF</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->role ?: '-' }}</td>
                    <td>{{ $employee->email ?: '-' }}</td>
                    <td>{{ $employee->phone_number ?: '-' }}</td>
                    <td>{{ $employee->address ?: '-' }}</td>
                    <td class="center">{{ $employee->identification_number ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Nenhum funcionario encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
