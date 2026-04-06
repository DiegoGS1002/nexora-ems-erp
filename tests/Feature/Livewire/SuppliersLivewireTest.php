<?php

use App\Livewire\Cadastro\Fornecedores\Form as SupplierForm;
use App\Livewire\Cadastro\Fornecedores\Index as SupplierIndex;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('creates a supplier through livewire form', function () {
    Livewire::test(SupplierForm::class)
        ->set('form.name', 'Contato Fornecedor')
        ->set('form.social_name', 'Fornecedor Teste LTDA')
        ->set('form.taxNumber', '12345678901234')
        ->set('form.email', 'fornecedor@example.com')
        ->set('form.phone_number', '11999999999')
        ->set('form.address_zip_code', '01000000')
        ->set('form.address_street', 'Rua Exemplo')
        ->set('form.address_number', '100')
        ->set('form.address_complement', 'Sala 10')
        ->set('form.address_district', 'Centro')
        ->set('form.address_city', 'Sao Paulo')
        ->set('form.address_state', 'SP')
        ->call('save')
        ->assertRedirect(route('suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'social_name' => 'Fornecedor Teste LTDA',
        'email' => 'fornecedor@example.com',
    ]);
});

it('updates a supplier through livewire form', function () {
    $supplier = Supplier::factory()->create([
        'social_name' => 'Fornecedor Antigo LTDA',
        'taxNumber' => '11111111111111',
        'email' => 'antigo@example.com',
    ]);

    Livewire::test(SupplierForm::class, ['supplier' => $supplier])
        ->set('form.name', 'Contato Novo')
        ->set('form.social_name', 'Fornecedor Novo LTDA')
        ->set('form.taxNumber', '22222222222222')
        ->set('form.email', 'novo@example.com')
        ->set('form.phone_number', '11977777777')
        ->set('form.address_zip_code', '03000000')
        ->set('form.address_street', 'Rua Nova')
        ->set('form.address_number', '300')
        ->set('form.address_complement', 'Conjunto 2')
        ->set('form.address_district', 'Bairro Novo')
        ->set('form.address_city', 'Campinas')
        ->set('form.address_state', 'SP')
        ->call('save')
        ->assertRedirect(route('suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'social_name' => 'Fornecedor Novo LTDA',
        'email' => 'novo@example.com',
    ]);
});

it('deletes a supplier from livewire index', function () {
    $supplier = Supplier::factory()->create([
        'social_name' => 'Fornecedor Delete LTDA',
        'taxNumber' => '33333333333333',
        'email' => 'delete@example.com',
    ]);

    Livewire::test(SupplierIndex::class)
        ->call('deleteSupplier', $supplier->id);

    $this->assertSoftDeleted('suppliers', [
        'id' => $supplier->id,
    ]);
});

