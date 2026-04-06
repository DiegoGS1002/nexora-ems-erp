<?php

use App\Services\BrasilAPIService;
use Illuminate\Support\Facades\Http;

it('retorna dados do CNPJ quando a API responde com sucesso', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/19131243000197' => Http::response([
            'razao_social' => 'EMPRESA TESTE LTDA',
            'nome_fantasia' => 'EMPRESA TESTE',
            'descricao_situacao_cadastral' => 'ATIVA',
            'logradouro' => 'RUA TESTE',
            'bairro' => 'CENTRO',
            'municipio' => 'SAO PAULO',
            'uf' => 'SP',
            'cep' => '01310-100',
        ], 200),
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCnpj('19.131.243/0001-97');

    expect($result)->toBeArray()
        ->and($result['razao_social'])->toBe('EMPRESA TESTE LTDA')
        ->and($result['descricao_situacao_cadastral'])->toBe('ATIVA');
});

it('retorna null quando o CNPJ nao e encontrado', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/*' => Http::response([], 404),
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCnpj('00000000000000');

    expect($result)->toBeNull();
});

it('retorna null quando a API de CNPJ esta indisponivel', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/*' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        },
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCnpj('19131243000197');

    expect($result)->toBeNull();
});

it('retorna dados do CEP quando a API responde com sucesso', function () {
    Http::fake([
        'brasilapi.com.br/api/cep/v2/01310100' => Http::response([
            'cep' => '01310-100',
            'state' => 'SP',
            'city' => 'São Paulo',
            'neighborhood' => 'Bela Vista',
            'street' => 'Avenida Paulista',
        ], 200),
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCep('01310-100');

    expect($result)->toBeArray()
        ->and($result['city'])->toBe('São Paulo')
        ->and($result['state'])->toBe('SP')
        ->and($result['street'])->toBe('Avenida Paulista');
});

it('retorna null quando o CEP nao e encontrado', function () {
    Http::fake([
        'brasilapi.com.br/api/cep/v2/*' => Http::response([], 404),
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCep('00000-000');

    expect($result)->toBeNull();
});

it('remove mascara antes de consultar o CNPJ', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/19131243000197' => Http::response(['razao_social' => 'TESTE'], 200),
    ]);

    $service = new BrasilAPIService();
    $result = $service->consultarCnpj('19.131.243/0001-97');

    expect($result)->not->toBeNull();

    Http::assertSent(fn($req) => str_contains($req->url(), '19131243000197'));
});


