<?php

use App\Enums\TipoPessoa;
use App\Livewire\Cadastro\Clientes\Form;
use App\Livewire\Cadastro\Clientes\Index;
use App\Models\Client;
use App\Services\BrasilAPIService;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('renderiza o formulario de criacao de cliente', function () {
    Livewire::test(Form::class)
        ->assertSee('Novo Cliente')
        ->assertSee('Pessoa Jurídica')
        ->assertSee('Pessoa Física');
});

it('renderiza o formulario de edicao com dados preenchidos', function () {
    $client = Client::factory()->create([
        'tipo_pessoa'   => TipoPessoa::PJ->value,
        'name'          => 'Empresa Teste',
        'social_name'   => 'Empresa Teste LTDA',
        'taxNumber'     => '19131243000197',
        'email'         => 'teste@empresa.com',
        'phone_number'  => '11999999999',
        'address_city'  => 'São Paulo',
        'address_state' => 'SP',
    ]);

    Livewire::test(Form::class, ['client' => $client])
        ->assertSee('Editar Cliente')
        ->assertSet('form.name', 'Empresa Teste')
        ->assertSet('form.email', 'teste@empresa.com');
});

it('valida campos obrigatorios ao salvar', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors(['form.name', 'form.taxNumber', 'form.email']);
});

it('preenche os campos automaticamente ao consultar CNPJ valido', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/19131243000197' => Http::response([
            'razao_social'                => 'EMPRESA TESTE LTDA',
            'nome_fantasia'               => 'EMPRESA TESTE',
            'descricao_situacao_cadastral'=> 'ATIVA',
            'cnae_fiscal_descricao'       => 'Desenvolvimento de software',
            'logradouro'                  => 'RUA DAS FLORES',
            'numero'                      => '100',
            'complemento'                 => '',
            'bairro'                      => 'CENTRO',
            'municipio'                   => 'SAO PAULO',
            'uf'                          => 'SP',
            'cep'                         => '01310-100',
        ], 200),
    ]);

    Livewire::test(Form::class)
        ->set('form.taxNumber', '19131243000197')
        ->call('buscarCnpj', app(BrasilAPIService::class))
        ->assertSet('form.social_name', 'EMPRESA TESTE LTDA')
        ->assertSet('form.name', 'EMPRESA TESTE')
        ->assertSet('form.address_city', 'SAO PAULO')
        ->assertSet('form.address_state', 'SP')
        ->assertSet('cnpjSituacao', 'ATIVA')
        ->assertSet('cnpjError', null);
});

it('exibe erro quando CNPJ nao e encontrado', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/*' => Http::response([], 404),
    ]);

    Livewire::test(Form::class)
        ->set('form.taxNumber', '00000000000000')
        ->call('buscarCnpj', app(BrasilAPIService::class))
        ->assertSet('cnpjError', 'CNPJ não encontrado ou serviço indisponível. Preencha os dados manualmente.');
});

it('preenche endereco automaticamente ao consultar CEP valido', function () {
    Http::fake([
        'brasilapi.com.br/api/cep/v2/01310100' => Http::response([
            'cep'          => '01310-100',
            'state'        => 'SP',
            'city'         => 'São Paulo',
            'neighborhood' => 'Bela Vista',
            'street'       => 'Avenida Paulista',
        ], 200),
    ]);

    Livewire::test(Form::class)
        ->set('form.address_zip_code', '01310100')
        ->call('buscarCep', app(BrasilAPIService::class))
        ->assertSet('form.address_street', 'Avenida Paulista')
        ->assertSet('form.address_city', 'São Paulo')
        ->assertSet('form.address_state', 'SP')
        ->assertSet('cepError', null);
});

it('salva novo cliente com sucesso', function () {
    Livewire::test(Form::class)
        ->set('form.tipo_pessoa', 'PJ')
        ->set('form.name', 'Empresa Nova')
        ->set('form.taxNumber', '19131243000197')
        ->set('form.email', 'empresa@nova.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clients.index'));

    expect(Client::where('email', 'empresa@nova.com')->exists())->toBeTrue();
});

it('atualiza cliente existente com sucesso', function () {
    $client = Client::factory()->create([
        'name'      => 'Nome Antigo',
        'taxNumber' => '19131243000197',
        'email'     => 'antigo@email.com',
    ]);

    Livewire::test(Form::class, ['client' => $client])
        ->set('form.name', 'Nome Atualizado')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clients.index'));

    expect($client->fresh()->name)->toBe('Nome Atualizado');
});

it('lista clientes na pagina de index', function () {
    Client::factory()->create(['name' => 'Cliente Alpha', 'email' => 'alpha@test.com', 'taxNumber' => '11111111111111']);
    Client::factory()->create(['name' => 'Cliente Beta',  'email' => 'beta@test.com',  'taxNumber' => '22222222222222']);

    Livewire::test(Index::class)
        ->assertSee('Cliente Alpha')
        ->assertSee('Cliente Beta');
});

it('filtra clientes pela busca', function () {
    Client::factory()->create(['name' => 'Empresa Visível',  'email' => 'visivel@test.com',   'taxNumber' => '11111111111111']);
    Client::factory()->create(['name' => 'Empresa Oculta',   'email' => 'oculta@test.com',    'taxNumber' => '22222222222222']);

    Livewire::test(Index::class)
        ->set('search', 'Visível')
        ->assertSee('Empresa Visível')
        ->assertDontSee('Empresa Oculta');
});

