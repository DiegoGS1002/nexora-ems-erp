<?php

use App\Livewire\Cadastro\Clientes\Form as ClientForm;
use App\Livewire\Cadastro\Clientes\Index as ClientIndex;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('creates a client through livewire form', function () {
    Livewire::test(ClientForm::class)
        ->set('form.name', 'Cliente Teste')
        ->set('form.social_name', 'Empresa Teste LTDA')
        ->set('form.taxNumber', '00.000.000/0001-00')
        ->set('form.email', 'cliente@example.com')
        ->set('form.phone_number', '(11) 99999-9999')
        ->set('form.address_street', 'Rua Exemplo')
        ->set('form.address_number', '100')
        ->set('form.address_city', 'São Paulo')
        ->set('form.address_state', 'SP')
        ->call('save')
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Cliente Teste',
        'email' => 'cliente@example.com',
    ]);
});

it('updates an existing client through livewire form', function () {
    $client = Client::factory()->create([
        'name' => 'Cliente Antigo',
        'taxNumber' => '11.111.111/0001-11',
        'email' => 'antigo@example.com',
    ]);

    Livewire::test(ClientForm::class, ['client' => $client])
        ->set('form.name', 'Cliente Novo')
        ->set('form.email', 'novo@example.com')
        ->set('form.taxNumber', '22.222.222/0001-22')
        ->set('form.phone_number', '(11) 97777-2222')
        ->call('save')
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Cliente Novo',
        'email' => 'novo@example.com',
    ]);
});

it('deletes a client from livewire index', function () {
    $client = Client::factory()->create([
        'name' => 'Cliente Delete',
        'taxNumber' => '33.333.333/0001-33',
        'email' => 'delete@example.com',
    ]);

    Livewire::test(ClientIndex::class)
        ->call('deleteClient', $client->id);

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
});

