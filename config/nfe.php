<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ambiente SEFAZ (1 = Produção, 2 = Homologação)
    |--------------------------------------------------------------------------
    */
    'environment' => (int) env('NFE_ENVIRONMENT', 2),

    /*
    |--------------------------------------------------------------------------
    | UF do emitente (ex: SP, RJ, MG...)
    |--------------------------------------------------------------------------
    */
    'uf' => env('NFE_UF', 'SP'),

    /*
    |--------------------------------------------------------------------------
    | Razão social e CNPJ fallback (caso a Company ativa não esteja cadastrada)
    |--------------------------------------------------------------------------
    */
    'razao_social' => env('NFE_RAZAO_SOCIAL', 'EMPRESA EXEMPLO LTDA'),
    'cnpj'         => env('NFE_CNPJ', '00000000000000'),

    /*
    |--------------------------------------------------------------------------
    | Dados fiscais do emitente
    |--------------------------------------------------------------------------
    */
    'inscricao_estadual' => env('NFE_IE', ''),
    'inscricao_municipal' => env('NFE_IM', ''),
    'cnae'    => env('NFE_CNAE', ''),
    'crt'     => (int) env('NFE_CRT', 1), // 1=Simples, 2=Simples excesso, 3=Regime Normal
    'ibge_emitente' => env('NFE_IBGE_MUNICIPIO', '3550308'), // cód IBGE do município do emitente

    /*
    |--------------------------------------------------------------------------
    | Certificado Digital (A1 — arquivo .pfx/.p12)
    |--------------------------------------------------------------------------
    */
    'cert_disk'     => env('NFE_CERT_DISK', 'local'),
    'cert_path'     => env('NFE_CERT_PATH', 'nfe/certificado.pfx'),
    'cert_password' => env('NFE_CERT_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Caminhos de armazenamento de XMLs e PDFs
    |--------------------------------------------------------------------------
    */
    'xml_disk'  => env('NFE_XML_DISK', 'local'),
    'xml_path'  => env('NFE_XML_PATH', 'nfe/xmls'),
    'danfe_path' => env('NFE_DANFE_PATH', 'nfe/danfe'),

    /*
    |--------------------------------------------------------------------------
    | Schemas da biblioteca sped-nfe (caminho dentro do vendor)
    |--------------------------------------------------------------------------
    */
    'schemes_path' => env(
        'NFE_SCHEMES_PATH',
        base_path('vendor/nfephp-org/sped-nfe/storage/schemes')
    ),

    /*
    |--------------------------------------------------------------------------
    | Token IBPT (tabela de incidência tributária — opcional)
    |--------------------------------------------------------------------------
    */
    'ibpt_token' => env('NFE_IBPT_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | CSC — Código de Segurança do Contribuinte (obrigatório para NF-Ce)
    |--------------------------------------------------------------------------
    */
    'csc'    => env('NFE_CSC', null),
    'csc_id' => env('NFE_CSC_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Versão da aplicação (exibida no XML)
    |--------------------------------------------------------------------------
    */
    'versao' => env('NFE_VERSAO_APP', '1.0.0'),
];

