<?php

use App\Models\Employees;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('baixa o PDF de funcionarios ao acessar rota de exportacao', function () {
    Employees::query()->create([
        'name' => 'Funcionario PDF',
        'identification_number' => '12345678901',
        'role' => 'Analista',
        'email' => 'pdf@funcionario.com',
        'phone_number' => '11999999999',
        'address' => 'Rua Teste, 123',
    ]);

    $response = $this->get(route('employees.print'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
    expect($response->headers->get('content-disposition'))->toContain('funcionarios-');
});

it('permite exportar PDF de funcionarios com filtro de busca', function () {
    Employees::query()->create([
        'name' => 'Funcionario Alfa',
        'identification_number' => '12345678902',
        'role' => 'RH',
        'email' => 'alfa@funcionario.com',
        'phone_number' => '11999999998',
        'address' => 'Rua Alfa, 10',
    ]);

    Employees::query()->create([
        'name' => 'Funcionario Beta',
        'identification_number' => '12345678903',
        'role' => 'Financeiro',
        'email' => 'beta@funcionario.com',
        'phone_number' => '11999999997',
        'address' => 'Rua Beta, 20',
    ]);

    $response = $this->get(route('employees.print', ['search' => 'Alfa']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

