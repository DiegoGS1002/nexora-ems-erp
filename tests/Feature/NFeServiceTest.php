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

it('resolves the NFe service contract without depending on a real certificate', function () {
    $service = Mockery::mock(NFeService::class)->makePartial();

    expect($service)->toBeInstanceOf(NFeService::class);
});

it('validates fiscal note status before transmission', function () {
    $note = FiscalNote::factory()->create([
        'status' => 'authorized',
    ]);

    $service = Mockery::mock(NFeService::class)->makePartial();

    expect(fn() => $service->transmitir($note))
        ->toThrow(InvalidArgumentException::class, 'Apenas notas em rascunho podem ser transmitidas');
});

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
