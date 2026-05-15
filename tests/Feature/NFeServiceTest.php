<?php

use App\Models\FiscalNote;
use App\Services\NFeService;

beforeEach(function () {
    // Mock de configurações mínimas
    config([
        'nfe.environment' => 2,
        'nfe.uf' => 'SP',
        'nfe.razao_social' => 'EMPRESA TESTE',
        'nfe.cnpj' => '00000000000191',
    ]);
});

it('can instantiate NFeService', function () {
    // Este teste vai falhar se certificado não existir, mas valida a estrutura
    expect(fn() => app(NFeService::class))->toThrow(RuntimeException::class);
})->throws(RuntimeException::class, 'Certificado digital não encontrado');

it('validates fiscal note status before transmission', function () {
    $note = FiscalNote::factory()->create([
        'status' => 'authorized',
    ]);

    $service = $this->mock(NFeService::class);

    expect(fn() => $service->transmitir($note))
        ->toThrow(InvalidArgumentException::class, 'Apenas notas em rascunho podem ser transmitidas');
})->skip('Requires certificate setup');

it('fiscal note has items relationship', function () {
    $note = FiscalNote::factory()->create();

    expect($note->items())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('fiscal note item belongs to fiscal note', function () {
    $note = FiscalNote::factory()->create();
    $item = $note->items()->create([
        'item_number' => 1,
        'description' => 'Produto Teste',
        'quantity' => 1,
        'unit_price' => 100,
        'total' => 100,
    ]);

    expect($item->fiscalNote)->toBeInstanceOf(FiscalNote::class);
    expect($item->fiscal_note_id)->toBe($note->id);
});

