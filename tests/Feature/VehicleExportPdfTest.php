<?php

use App\Models\Vehicle;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('baixa o PDF de veiculos ao acessar rota de exportacao', function () {
    Vehicle::factory()->create([
        'plate' => 'ABC1D23',
        'renavam' => '12345678901',
        'chassis' => '9BWZZZ377VT004251',
        'brand' => 'Volkswagen',
        'model' => 'Gol',
    ]);

    $response = $this->get(route('vehicles.print'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
    expect($response->headers->get('content-disposition'))->toContain('veiculos-');
});

it('permite exportar PDF de veiculos com filtro de busca', function () {
    Vehicle::factory()->create([
        'plate' => 'AAA1A11',
        'renavam' => '12345678902',
        'chassis' => '9BWZZZ377VT004252',
        'brand' => 'Fiat',
        'model' => 'Uno',
    ]);

    Vehicle::factory()->create([
        'plate' => 'BBB2B22',
        'renavam' => '12345678903',
        'chassis' => '9BWZZZ377VT004253',
        'brand' => 'Toyota',
        'model' => 'Corolla',
    ]);

    $response = $this->get(route('vehicles.print', ['search' => 'Fiat']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

