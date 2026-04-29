{{--
  Partial: PDF Header
  Variáveis esperadas:
    $logo         – data-URI base64 da logo (empresa ou Nexora), pode ser null
    $company_name – nome fantasia da empresa vinculada ao usuário
    $title        – título do relatório (ex: "Relatório de Clientes")
    $printedAt    – Carbon da data/hora de geração
    $recordCount  – (int) total de registros
    $search       – (string, opcional) filtro aplicado
--}}
<style>
    .pdf-header {
        display: table;
        width: 100%;
        margin-bottom: 18px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 12px;
    }
    .pdf-header-logo {
        display: table-cell;
        width: 72px;
        vertical-align: middle;
    }
    .pdf-header-logo img {
        max-width: 64px;
        max-height: 64px;
        object-fit: contain;
    }
    .pdf-header-info {
        display: table-cell;
        vertical-align: middle;
        padding-left: 12px;
    }
    .pdf-company-name {
        font-size: 11px;
        color: #6b7280;
        margin: 0 0 2px 0;
    }
    .pdf-title {
        font-size: 18px;
        font-weight: bold;
        color: #111827;
        margin: 0;
    }
    .pdf-meta {
        font-size: 10px;
        color: #6b7280;
        margin-top: 4px;
    }
</style>

<div class="pdf-header">
    @if($logo)
        <div class="pdf-header-logo">
            <img src="{{ $logo }}" alt="{{ $company_name }}">
        </div>
    @endif
    <div class="pdf-header-info">
        <p class="pdf-company-name">{{ $company_name }}</p>
        <h1 class="pdf-title">{{ $title }}</h1>
        <div class="pdf-meta">
            Gerado em: {{ $printedAt->format('d/m/Y H:i') }}
            &nbsp;|&nbsp; Total de registros: {{ $recordCount }}
            @if(!empty($search))
                &nbsp;|&nbsp; Filtro: {{ $search }}
            @endif
        </div>
    </div>
</div>

