<?php

use App\Models\Supplier;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('baixa o PDF de fornecedores ao acessar rota de exportacao', function () {
    Supplier::factory()->create([
        'social_name' => 'Fornecedor PDF LTDA',
        'taxNumber' => '12345678000199',
        'email' => 'pdf@fornecedor.com',
    ]);

    $response = $this->get(route('suppliers.print'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
    expect($response->headers->get('content-disposition'))->toContain('fornecedores-');
});

it('permite exportar PDF de fornecedores com filtro de busca', function () {
    Supplier::factory()->create(['social_name' => 'Fornecedor Alfa']);
    Supplier::factory()->create(['social_name' => 'Fornecedor Beta']);

    $response = $this->get(route('suppliers.print', ['search' => 'Alfa']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

