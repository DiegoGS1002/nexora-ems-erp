<?php

use App\Enums\CategoriaVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\EspecieVeiculo;
use App\Enums\TipoVeiculo;
use App\Livewire\Cadastro\Veiculos\Form;
use App\Livewire\Cadastro\Veiculos\Index;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->state(['last_login_at' => now()])->create());
});

// ══════════════════════════════════════════════════
// RENDERIZAÇÃO
// ══════════════════════════════════════════════════

it('renderiza a página de listagem de veículos', function () {
    $this->get('/vehicles')
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('renderiza a página de criação com abas e campos esperados', function () {
    Livewire::test(Form::class)
        ->assertSee('Novo Veículo')
        ->assertSee('Dados Gerais')
        ->assertSee('Documentos')
        ->assertSee('Manutenção')
        ->assertSee('Seguro')
        ->assertSee('Custos')
        ->assertSee('Observações')
        ->assertSee('Histórico')
        ->assertSee('Identificação do Veículo')
        ->assertSee('Placa')
        ->assertSee('RENAVAM')
        ->assertSee('Chassi');
});

it('renderiza a página de edição com badge Editando e dados preenchidos', function () {
    $vehicle = Vehicle::factory()->create([
        'plate'   => 'ABC1D23',
        'brand'   => 'Toyota',
        'model'   => 'Corolla',
        'renavam' => '12345678901',
        'chassis' => '9BWZZZ377VT004251',
    ]);

    Livewire::test(Form::class, ['vehicle' => $vehicle])
        ->assertSee('Editar Veículo')
        ->assertSee('Editando')
        ->assertSet('form.plate', 'ABC1D23')
        ->assertSet('form.brand', 'Toyota')
        ->assertSet('form.model', 'Corolla');
});

// ══════════════════════════════════════════════════
// VALIDAÇÃO
// ══════════════════════════════════════════════════

it('valida campos obrigatórios ao tentar salvar vazio', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors([
            'form.plate',
            'form.renavam',
            'form.chassis',
            'form.vehicle_type',
            'form.category',
            'form.species',
            'form.manufacturing_year',
            'form.model_year',
            'form.brand',
            'form.model',
            'form.color',
            'form.fuel_type',
        ]);
});

it('valida unicidade da placa', function () {
    Vehicle::factory()->create(['plate' => 'AAA0000', 'renavam' => '00000000001', 'chassis' => 'AAAAAAAAAAAAA0001']);

    Livewire::test(Form::class)
        ->set('form.plate', 'AAA0000')
        ->set('form.renavam', '00000000002')
        ->set('form.chassis', 'AAAAAAAAAAAAA0002')
        ->set('form.vehicle_type', TipoVeiculo::Passeio->value)
        ->set('form.category', CategoriaVeiculo::Particular->value)
        ->set('form.species', EspecieVeiculo::Passageiro->value)
        ->set('form.manufacturing_year', '2020')
        ->set('form.model_year', '2021')
        ->set('form.brand', 'Ford')
        ->set('form.model', 'Ka')
        ->set('form.color', 'Branco')
        ->set('form.fuel_type', CombustivelVeiculo::Flex->value)
        ->call('save')
        ->assertHasErrors(['form.plate']);
});

it('valida unicidade do renavam', function () {
    Vehicle::factory()->create(['plate' => 'AAA1111', 'renavam' => '99999999999', 'chassis' => 'AAAAAAAAAAAAA1111']);

    Livewire::test(Form::class)
        ->set('form.plate', 'BBB2222')
        ->set('form.renavam', '99999999999')
        ->set('form.chassis', 'AAAAAAAAAAAAA2222')
        ->set('form.vehicle_type', TipoVeiculo::Passeio->value)
        ->set('form.category', CategoriaVeiculo::Particular->value)
        ->set('form.species', EspecieVeiculo::Passageiro->value)
        ->set('form.manufacturing_year', '2020')
        ->set('form.model_year', '2021')
        ->set('form.brand', 'Fiat')
        ->set('form.model', 'Uno')
        ->set('form.color', 'Prata')
        ->set('form.fuel_type', CombustivelVeiculo::Flex->value)
        ->call('save')
        ->assertHasErrors(['form.renavam']);
});

// ══════════════════════════════════════════════════
// CRIAÇÃO
// ══════════════════════════════════════════════════

it('cria um veículo com todos os campos obrigatórios preenchidos', function () {
    Livewire::test(Form::class)
        ->set('form.plate', 'XYZ5A67')
        ->set('form.renavam', '12345678902')
        ->set('form.chassis', 'AAAAAAAAAAAAA5678')
        ->set('form.vehicle_type', TipoVeiculo::Utilitario->value)
        ->set('form.category', CategoriaVeiculo::Frota->value)
        ->set('form.species', EspecieVeiculo::Misto->value)
        ->set('form.manufacturing_year', '2022')
        ->set('form.model_year', '2023')
        ->set('form.brand', 'Mercedes')
        ->set('form.model', 'Sprinter')
        ->set('form.color', 'Branco')
        ->set('form.fuel_type', CombustivelVeiculo::Diesel->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('vehicles.index'));

    expect(Vehicle::where('plate', 'XYZ5A67')->exists())->toBeTrue();
});

it('cria veículo com status ativo por padrão', function () {
    Livewire::test(Form::class)
        ->set('form.plate', 'TST1B11')
        ->set('form.renavam', '11111111111')
        ->set('form.chassis', 'TTTTTTTTTTTTT1111')
        ->set('form.vehicle_type', TipoVeiculo::Passeio->value)
        ->set('form.category', CategoriaVeiculo::Particular->value)
        ->set('form.species', EspecieVeiculo::Passageiro->value)
        ->set('form.manufacturing_year', '2023')
        ->set('form.model_year', '2024')
        ->set('form.brand', 'Toyota')
        ->set('form.model', 'HiLux')
        ->set('form.color', 'Preto')
        ->set('form.fuel_type', CombustivelVeiculo::Diesel->value)
        ->call('save')
        ->assertHasNoErrors();

    $vehicle = Vehicle::where('plate', 'TST1B11')->first();
    expect($vehicle->is_active)->toBeTrue();
});

it('cria veículo com campos opcionais preenchidos', function () {
    Livewire::test(Form::class)
        ->set('form.plate', 'OPT2C22')
        ->set('form.renavam', '22222222222')
        ->set('form.chassis', 'OOOOOOOOOOOOO2222')
        ->set('form.vehicle_type', TipoVeiculo::Caminhao->value)
        ->set('form.category', CategoriaVeiculo::Oficial->value)
        ->set('form.species', EspecieVeiculo::Carga->value)
        ->set('form.manufacturing_year', '2021')
        ->set('form.model_year', '2022')
        ->set('form.brand', 'Volkswagen')
        ->set('form.model', 'Delivery')
        ->set('form.color', 'Azul')
        ->set('form.fuel_type', CombustivelVeiculo::Diesel->value)
        ->set('form.responsible_driver', 'João Silva')
        ->set('form.department', 'logistica')
        ->set('form.observations', 'Veículo para entregas regionais')
        ->set('form.is_active', false)
        ->call('save')
        ->assertHasNoErrors();

    $vehicle = Vehicle::where('plate', 'OPT2C22')->first();
    expect($vehicle->responsible_driver)->toBe('João Silva');
    expect($vehicle->is_active)->toBeFalse();
    expect($vehicle->observations)->toBe('Veículo para entregas regionais');
});

// ══════════════════════════════════════════════════
// EDIÇÃO
// ══════════════════════════════════════════════════

it('atualiza um veículo existente', function () {
    $vehicle = Vehicle::factory()->create([
        'plate'              => 'EDI3D33',
        'renavam'            => '33333333333',
        'chassis'            => 'EEEEEEEEEEEEE3333',
        'brand'              => 'Fiat',
        'model'              => 'Ducato',
        'vehicle_type'       => TipoVeiculo::VanFurgao->value,
        'category'           => CategoriaVeiculo::Frota->value,
        'species'            => EspecieVeiculo::Misto->value,
        'manufacturing_year' => '2019',
        'model_year'         => '2020',
        'color'              => 'Branco',
        'fuel_type'          => CombustivelVeiculo::Diesel->value,
    ]);

    Livewire::test(Form::class, ['vehicle' => $vehicle])
        ->set('form.brand', 'Mercedes')
        ->set('form.model', 'Sprinter')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('vehicles.index'));

    expect($vehicle->fresh()->brand)->toBe('Mercedes');
    expect($vehicle->fresh()->model)->toBe('Sprinter');
});

it('não valida unicidade da placa do próprio veículo ao editar', function () {
    $vehicle = Vehicle::factory()->create([
        'plate'              => 'PROP444',
        'renavam'            => '44444444444',
        'chassis'            => 'PPPPPPPPPPPPP4444',
        'brand'              => 'Honda',
        'model'              => 'Civic',
        'vehicle_type'       => TipoVeiculo::Passeio->value,
        'category'           => CategoriaVeiculo::Particular->value,
        'species'            => EspecieVeiculo::Passageiro->value,
        'manufacturing_year' => '2020',
        'model_year'         => '2021',
        'color'              => 'Prata',
        'fuel_type'          => CombustivelVeiculo::Flex->value,
    ]);

    Livewire::test(Form::class, ['vehicle' => $vehicle])
        ->set('form.plate', 'PROP444')
        ->call('save')
        ->assertHasNoErrors(['form.plate']);
});

// ══════════════════════════════════════════════════
// LISTAGEM E BUSCA
// ══════════════════════════════════════════════════

it('exibe os veículos cadastrados na listagem', function () {
    Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'Corolla', 'plate' => 'LST5E55', 'renavam' => '55555555555', 'chassis' => 'LLLLLLLLLLLLL5555']);
    Vehicle::factory()->create(['brand' => 'Honda', 'model' => 'HRV', 'plate' => 'LST6F66', 'renavam' => '66666666666', 'chassis' => 'LLLLLLLLLLLLL6666']);

    Livewire::test(Index::class)
        ->assertSee('Toyota')
        ->assertSee('Corolla')
        ->assertSee('Honda');
});

it('filtra veículos pelo campo de busca', function () {
    Vehicle::factory()->create(['brand' => 'BMW', 'model' => 'X5', 'plate' => 'SRC7G77', 'renavam' => '77777777777', 'chassis' => 'SSSSSSSSSSSSS7777']);
    Vehicle::factory()->create(['brand' => 'Audi', 'model' => 'A4', 'plate' => 'SRC8H88', 'renavam' => '88888888888', 'chassis' => 'SSSSSSSSSSSSS8888']);

    Livewire::test(Index::class)
        ->set('search', 'BMW')
        ->assertSee('BMW')
        ->assertDontSee('Audi');
});

it('exibe estado vazio quando não há veículos', function () {
    Livewire::test(Index::class)
        ->assertSee('Nenhum veículo cadastrado');
});

// ══════════════════════════════════════════════════
// EXCLUSÃO
// ══════════════════════════════════════════════════

it('exclui um veículo da listagem', function () {
    $vehicle = Vehicle::factory()->create([
        'plate'   => 'DEL9I99',
        'renavam' => '99999999999',
        'chassis' => 'DDDDDDDDDDDDD9999',
        'brand'   => 'Renault',
        'model'   => 'Master',
    ]);

    Livewire::test(Index::class)
        ->call('deleteVehicle', $vehicle->id);

    expect(Vehicle::where('id', $vehicle->id)->exists())->toBeFalse();
});




