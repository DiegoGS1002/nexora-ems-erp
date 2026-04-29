<?php

use App\Models\Client;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('baixa o PDF de clientes ao acessar rota de exportacao', function () {
    Client::factory()->create([
        'name' => 'Cliente PDF',
        'taxNumber' => '12345678000199',
        'email' => 'pdf@cliente.com',
    ]);

    $response = $this->get(route('clients.print'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
    expect($response->headers->get('content-disposition'))->toContain('clientes-');
});

it('permite exportar PDF de clientes com filtro de busca', function () {
    Client::factory()->create(['name' => 'Cliente Alfa']);
    Client::factory()->create(['name' => 'Cliente Beta']);

    $response = $this->get(route('clients.print', ['search' => 'Alfa']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

