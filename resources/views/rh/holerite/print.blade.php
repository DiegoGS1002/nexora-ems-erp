<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holerite — {{ $payroll->employee?->name }} — {{ $payroll->reference_month?->translatedFormat('F/Y') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════
           BASE
        ═══════════════════════════════════════════════ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: #F3F2F8;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            padding: 24px;
        }

        /* ─── TOOLBAR (no-print) ─── */
        .hp-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 820px;
            margin: 0 auto 16px auto;
        }

        .hp-toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hp-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-family: inherit;
            transition: all 0.15s ease;
        }

        .hp-btn-primary {
            background: #3B82F6;
            color: #fff;
        }

        .hp-btn-primary:hover { background: #2563EB; }

        .hp-btn-ghost {
            background: rgba(255,255,255,0.7);
            color: #475569;
            border: 1px solid rgba(148,163,184,0.3);
        }

        .hp-btn-ghost:hover { background: #fff; }

        .hp-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }

        .hp-badge-draft    { background: #F1F5F9; color: #475569; }
        .hp-badge-closed   { background: #FEF3C7; color: #92400E; }
        .hp-badge-paid     { background: #DCFCE7; color: #15803D; }

        /* ═══════════════════════════════════════════════
           DOCUMENT CARD
        ═══════════════════════════════════════════════ */
        .hp-doc {
            max-width: 820px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(30, 41, 59, 0.12), 0 1px 4px rgba(30, 41, 59, 0.08);
        }

        /* ─── DOCUMENT HEADER ─── */
        .hp-header {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            padding: 28px 36px;
            gap: 24px;
        }

        .hp-header-company {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            flex: 1;
        }

        .hp-company-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,0.1);
            border: 1.5px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hp-company-name {
            font-size: 18px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .hp-company-cnpj {
            font-size: 12px;
            color: rgba(255,255,255,0.55);
            margin-top: 4px;
            font-weight: 500;
        }

        .hp-company-address {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            margin-top: 3px;
        }

        .hp-header-title-wrap {
            text-align: right;
            flex-shrink: 0;
        }

        .hp-doc-type {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 6px;
        }

        .hp-doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }

        .hp-doc-competencia {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            margin-top: 6px;
            font-weight: 500;
        }

        .hp-doc-pagamento {
            font-size: 11.5px;
            color: rgba(255,255,255,0.5);
            margin-top: 3px;
        }

        .hp-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .hp-status-draft  { background: rgba(148,163,184,0.2); color: #CBD5E1; }
        .hp-status-closed { background: rgba(217,119,6,0.25); color: #FCD34D; }
        .hp-status-paid   { background: rgba(16,185,129,0.25); color: #6EE7B7; }

        /* ─── EMPLOYEE SECTION ─── */
        .hp-section-label {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            background: #F8FAFC;
            border-bottom: 1px solid #E8EEF5;
        }

        .hp-emp-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-bottom: 2px solid #E2E8F0;
        }

        .hp-emp-field {
            padding: 14px 20px;
            border-right: 1px solid #E8EEF5;
            border-bottom: 1px solid #E8EEF5;
        }

        .hp-emp-field:nth-child(4n) { border-right: none; }

        .hp-field-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94A3B8;
            margin-bottom: 4px;
        }

        .hp-field-value {
            font-size: 13px;
            font-weight: 600;
            color: #1E293B;
            line-height: 1.3;
        }

        .hp-field-value--highlight {
            color: #1D4ED8;
            font-size: 14px;
            font-weight: 700;
        }

        /* ─── EVENTS TABLE ─── */
        .hp-events-wrap {
            border-bottom: 2px solid #E2E8F0;
        }

        .hp-events-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .hp-events-col {
            border-right: 1px solid #E8EEF5;
        }

        .hp-events-col:last-child { border-right: none; }

        .hp-col-header {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid;
        }

        .hp-col-header--earning {
            background: #F0FDF4;
            color: #15803D;
            border-color: #BBF7D0;
        }

        .hp-col-header--deduction {
            background: #FFF5F5;
            color: #B91C1C;
            border-color: #FECACA;
        }

        .hp-events-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .hp-events-table thead tr {
            background: #F1F5FA;
        }

        .hp-events-table th {
            padding: 8px 16px;
            text-align: left;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94A3B8;
            border-bottom: 1px solid #E8EEF5;
        }

        .hp-events-table th.right,
        .hp-events-table td.right { text-align: right; }

        .hp-events-table tbody tr { background: #FFFFFF; }

        .hp-events-table tbody tr:nth-child(even) { background: #F8FAFD; }

        .hp-events-table tbody tr.base-row { background: #EEF2FF; }

        .hp-events-table td {
            padding: 9px 16px;
            color: #334155;
            font-size: 12.5px;
            border-bottom: 1px solid rgba(226,232,240,0.6);
        }

        .hp-events-table tbody tr:last-child td { border-bottom: none; }

        .hp-events-table tfoot tr { background: #F1F5FA; }

        .hp-events-table tfoot td {
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 700;
            color: #1E293B;
            border-top: 1.5px solid #CBD5E1;
        }

        .hp-empty-row td {
            text-align: center;
            color: #94A3B8 !important;
            font-style: italic;
            font-size: 11.5px !important;
            padding: 18px !important;
        }

        .val-earning {
            color: #15803D;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .val-deduction {
            color: #B91C1C;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .val-mono {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        /* ─── BASES DE CÁLCULO ─── */
        .hp-bases-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-bottom: 2px solid #E2E8F0;
        }

        .hp-base-item {
            padding: 14px 20px;
            border-right: 1px solid #E8EEF5;
            background: #FAFBFF;
        }

        .hp-base-item:last-child { border-right: none; }

        .hp-base-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94A3B8;
            margin-bottom: 5px;
        }

        .hp-base-value {
            font-size: 15px;
            font-weight: 700;
            color: #475569;
            font-family: 'Courier New', monospace;
        }

        /* ─── TOTALS ─── */
        .hp-totals {
            border-bottom: 2px solid #E2E8F0;
        }

        .hp-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 36px;
            border-bottom: 1px solid #EEF2F8;
        }

        .hp-total-row:last-child { border-bottom: none; }

        .hp-total-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #475569;
        }

        .hp-total-value {
            font-size: 16px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: #1E293B;
        }

        .hp-total-row--earning  .hp-total-value { color: #15803D; }
        .hp-total-row--deduction .hp-total-value { color: #B91C1C; }

        .hp-total-row--net {
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            padding: 16px 36px;
        }

        .hp-total-row--net .hp-total-label {
            font-size: 14px;
            font-weight: 700;
            color: #3730A3;
            text-transform: uppercase;
        }

        .hp-total-row--net .hp-total-value {
            font-size: 24px;
            color: #1D4ED8;
        }

        /* ─── SIGNATURE ─── */
        .hp-signature {
            display: flex;
            justify-content: center;
            gap: 80px;
            padding: 32px 36px;
            border-bottom: 1px solid #E8EEF5;
        }

        .hp-sig-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 200px;
        }

        .hp-sig-line {
            width: 100%;
            height: 1px;
            background: #94A3B8;
            margin-bottom: 6px;
        }

        .hp-sig-name {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }

        .hp-sig-role {
            font-size: 10px;
            color: #94A3B8;
            font-weight: 500;
        }

        /* ─── FOOTER ─── */
        .hp-footer {
            padding: 14px 36px;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .hp-footer-text {
            font-size: 10px;
            color: #94A3B8;
            line-height: 1.5;
            flex: 1;
        }

        .hp-footer-ref {
            font-size: 10px;
            color: #CBD5E1;
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
        }

        /* ═══════════════════════════════════════════════
           PRINT MEDIA
        ═══════════════════════════════════════════════ */
        @media print {
            .hp-toolbar { display: none !important; }

            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }

            .hp-doc {
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                margin: 0;
            }

            .hp-header {
                background: #1E293B !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-col-header--earning {
                background: #F0FDF4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-col-header--deduction {
                background: #FFF5F5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-events-table tbody tr:nth-child(even) {
                background: #F8FAFD !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-events-table tbody tr.base-row {
                background: #EEF2FF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-total-row--net {
                background: #EEF2FF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-bases-grid .hp-base-item {
                background: #FAFBFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hp-footer {
                background: #F8FAFC !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
        }

        @media (max-width: 700px) {
            body { padding: 12px; }

            .hp-header { flex-direction: column; gap: 16px; }
            .hp-header-title-wrap { text-align: left; }

            .hp-emp-grid { grid-template-columns: 1fr 1fr; }
            .hp-emp-field:nth-child(2n) { border-right: none; }
            .hp-emp-field:nth-child(4n) { border-right: 1px solid #E8EEF5; }

            .hp-events-grid { grid-template-columns: 1fr; }
            .hp-events-col { border-right: none; border-bottom: 1px solid #E8EEF5; }

            .hp-bases-grid { grid-template-columns: 1fr 1fr; }

            .hp-signature { flex-direction: column; align-items: center; gap: 32px; }
        }
    </style>
</head>
<body>

    {{-- ══════════════════════════════════════════
         TOOLBAR (hidden when printing)
    ══════════════════════════════════════════ --}}
    <div class="hp-toolbar">
        <div class="hp-toolbar-left">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('holerite.index') }}"
               class="hp-btn hp-btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar
            </a>
            <span class="hp-badge hp-badge-{{ $payroll->status->value }}">
                {{ $payroll->status->label() }}
            </span>
        </div>
        <button onclick="window.print()" class="hp-btn hp-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Imprimir / Salvar PDF
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         DOCUMENTO
    ══════════════════════════════════════════ --}}
    <div class="hp-doc">

        {{-- ── CABEÇALHO ── --}}
        <div class="hp-header">
            <div class="hp-header-company">
                <div class="hp-company-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div>
                    <div class="hp-company-name">{{ $company['name'] }}</div>
                    @if($company['cnpj'])
                        <div class="hp-company-cnpj">CNPJ: {{ $company['cnpj'] }}</div>
                    @endif
                    @if($company['address'])
                        <div class="hp-company-address">
                            {{ $company['address'] }}@if($company['city']), {{ $company['city'] }}@endif@if($company['state'])/{{ $company['state'] }}@endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="hp-header-title-wrap">
                <div class="hp-doc-type">Recursos Humanos</div>
                <div class="hp-doc-title">Recibo de Pagamento<br>de Salário</div>
                <div class="hp-doc-competencia">
                    Competência: {{ $payroll->reference_month?->translatedFormat('F/Y') }}
                </div>
                @if($payroll->payment_date)
                    <div class="hp-doc-pagamento">
                        Pagamento: {{ $payroll->payment_date->format('d/m/Y') }}
                    </div>
                @endif
                <div class="hp-status-pill hp-status-{{ $payroll->status->value }}">
                    {{ $payroll->status->label() }}
                </div>
            </div>
        </div>

        {{-- ── DADOS DO COLABORADOR ── --}}
        <div class="hp-section-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Dados do Colaborador
        </div>
        <div class="hp-emp-grid">
            <div class="hp-emp-field">
                <div class="hp-field-label">Nome Completo</div>
                <div class="hp-field-value">{{ $payroll->employee?->name ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">CPF</div>
                <div class="hp-field-value">{{ $payroll->employee?->identification_number ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Matrícula</div>
                <div class="hp-field-value">{{ $payroll->employee?->internal_code ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Cargo</div>
                <div class="hp-field-value">{{ $payroll->employee?->role ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Departamento</div>
                <div class="hp-field-value">{{ $payroll->employee?->department ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Admissão</div>
                <div class="hp-field-value">{{ $payroll->employee?->admission_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Jornada</div>
                <div class="hp-field-value">{{ $payroll->employee?->work_schedule ?? '—' }}</div>
            </div>
            <div class="hp-emp-field">
                <div class="hp-field-label">Salário Base</div>
                <div class="hp-field-value hp-field-value--highlight">
                    R$ {{ number_format($baseSalary, 2, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ── DEMONSTRATIVO ── --}}
        <div class="hp-section-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/></svg>
            Demonstrativo de Pagamento
        </div>

        <div class="hp-events-wrap">
            <div class="hp-events-grid">

                {{-- PROVENTOS --}}
                <div class="hp-events-col">
                    <div class="hp-col-header hp-col-header--earning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Proventos
                    </div>
                    <table class="hp-events-table">
                        <thead>
                            <tr>
                                <th style="width:40px">Cód.</th>
                                <th>Descrição</th>
                                <th class="right" style="width:110px">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Salário Base sempre primeiro --}}
                            <tr class="base-row">
                                <td>001</td>
                                <td>Salário Base</td>
                                <td class="right val-earning">{{ number_format($baseSalary, 2, ',', '.') }}</td>
                            </tr>
                            @forelse($earnings as $i => $item)
                                <tr>
                                    <td>{{ str_pad($i + 2, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="right val-earning">{{ number_format((float) $item->amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr class="hp-empty-row">
                                    <td colspan="3">Nenhum provento adicional</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total Proventos</td>
                                <td class="right val-mono">{{ number_format($totalVencimentos, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- DESCONTOS --}}
                <div class="hp-events-col">
                    <div class="hp-col-header hp-col-header--deduction">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Descontos
                    </div>
                    <table class="hp-events-table">
                        <thead>
                            <tr>
                                <th style="width:40px">Cód.</th>
                                <th>Descrição</th>
                                <th class="right" style="width:110px">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deductions as $i => $item)
                                <tr>
                                    <td>{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="right val-deduction">{{ number_format((float) $item->amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr class="hp-empty-row">
                                    <td colspan="3">Nenhum desconto lançado</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total Descontos</td>
                                <td class="right val-mono">{{ number_format($totalDeductions, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

        {{-- ── BASES DE CÁLCULO ── --}}
        <div class="hp-section-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Bases de Cálculo
        </div>
        <div class="hp-bases-grid">
            <div class="hp-base-item">
                <div class="hp-base-label">Base INSS</div>
                <div class="hp-base-value">R$ {{ number_format($baseInss, 2, ',', '.') }}</div>
            </div>
            <div class="hp-base-item">
                <div class="hp-base-label">Base FGTS (8%)</div>
                <div class="hp-base-value">R$ {{ number_format($baseFgts, 2, ',', '.') }}</div>
            </div>
            <div class="hp-base-item">
                <div class="hp-base-label">Base IRRF</div>
                <div class="hp-base-value">R$ {{ number_format($baseIrrf, 2, ',', '.') }}</div>
            </div>
        </div>

        {{-- ── TOTAIS ── --}}
        <div class="hp-totals">
            <div class="hp-total-row hp-total-row--earning">
                <span class="hp-total-label">Total de Vencimentos</span>
                <span class="hp-total-value">R$ {{ number_format($totalVencimentos, 2, ',', '.') }}</span>
            </div>
            <div class="hp-total-row hp-total-row--deduction">
                <span class="hp-total-label">Total de Descontos</span>
                <span class="hp-total-value">R$ {{ number_format($totalDeductions, 2, ',', '.') }}</span>
            </div>
            <div class="hp-total-row hp-total-row--net">
                <span class="hp-total-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align:middle;margin-right:6px"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Salário Líquido a Receber
                </span>
                <span class="hp-total-value">R$ {{ number_format($netSalary, 2, ',', '.') }}</span>
            </div>
        </div>

        {{-- ── ASSINATURAS ── --}}
        <div class="hp-signature">
            <div class="hp-sig-item">
                <div class="hp-sig-line"></div>
                <span class="hp-sig-name">{{ $company['name'] }}</span>
                <span class="hp-sig-role">Empregador / Responsável</span>
            </div>
            <div class="hp-sig-item">
                <div class="hp-sig-line"></div>
                <span class="hp-sig-name">{{ $payroll->employee?->name ?? '—' }}</span>
                <span class="hp-sig-role">Colaborador / Empregado</span>
            </div>
        </div>

        {{-- ── RODAPÉ ── --}}
        <div class="hp-footer">
            <p class="hp-footer-text">
                Declaro ter recebido a importância líquida discriminada neste recibo, referente à competência
                {{ $payroll->reference_month?->translatedFormat('F/Y') }}, em conformidade com a CLT e legislações trabalhistas vigentes.
                @if($payroll->observations)
                    <br><strong>Obs.:</strong> {{ $payroll->observations }}
                @endif
            </p>
            <div class="hp-footer-ref">
                <div>Holerite Nº {{ str_pad($payroll->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div>Gerado em {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

    </div>{{-- /.hp-doc --}}

    <script>
        // Auto focus ready for print
        window.addEventListener('load', function () {
            document.title = 'Holerite — {{ addslashes($payroll->employee?->name ?? '') }} — {{ $payroll->reference_month?->translatedFormat('F Y') }}';
        });
    </script>

</body>
</html>

