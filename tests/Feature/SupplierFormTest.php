<?php

use App\Livewire\Cadastro\Fornecedores\Form;
use App\Livewire\Cadastro\Fornecedores\Index;
use App\Models\Supplier;
use App\Services\BrasilAPIService;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

it('renderiza o formulario de criacao de fornecedor', function () {
    Livewire::test(Form::class)
        ->assertSee('Novo Fornecedor')
        ->assertSee('CNPJ')
        ->assertSee('Consultar CNPJ');
});

it('valida campos obrigatorios ao salvar fornecedor', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors([
            'form.social_name',
            'form.taxNumber',
            'form.email',
        ]);
});

it('preenche os campos automaticamente ao consultar CNPJ valido', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/19131243000197' => Http::response([
            'razao_social'                => 'FORNECEDOR TESTE LTDA',
            'nome_fantasia'               => 'FORNECEDOR TESTE',
            'descricao_situacao_cadastral'=> 'ATIVA',
            'cnae_fiscal_descricao'       => 'Comércio atacadista',
            'logradouro'                  => 'AV BRASIL',
            'numero'                      => '500',
            'complemento'                 => 'SALA 1',
            'bairro'                      => 'INDUSTRIAL',
            'municipio'                   => 'CAMPINAS',
            'uf'                          => 'SP',
            'cep'                         => '13050-000',
        ], 200),
    ]);

    Livewire::test(Form::class)
        ->set('form.taxNumber', '19131243000197')
        ->call('buscarCnpj', app(BrasilAPIService::class))
        ->assertSet('form.social_name', 'FORNECEDOR TESTE LTDA')
        ->assertSet('form.name', 'FORNECEDOR TESTE')
        ->assertSet('form.address_city', 'CAMPINAS')
        ->assertSet('form.address_state', 'SP')
        ->assertSet('cnpjSituacao', 'ATIVA')
        ->assertSet('cnpjError', null);
});

it('exibe alerta quando situacao cadastral nao e ativa', function () {
    Http::fake([
        'brasilapi.com.br/api/cnpj/v1/*' => Http::response([
            'razao_social'                => 'EMPRESA SUSPENSA',
            'nome_fantasia'               => '',
            'descricao_situacao_cadastral'=> 'SUSPENSA',
            'logradouro'                  => '',
            'bairro'                      => '',
            'municipio'                   => '',
            'uf'                          => '',
            'cep'                         => '',
        ], 200),
    ]);

    Livewire::test(Form::class)
        ->set('form.taxNumber', '11222333000144')
        ->call('buscarCnpj', app(BrasilAPIService::class))
        ->assertSet('cnpjSituacao', 'SUSPENSA');
});

it('preenche endereco ao consultar CEP valido', function () {
    Http::fake([
        'brasilapi.com.br/api/cep/v2/13050000' => Http::response([
            'cep'          => '13050-000',
            'state'        => 'SP',
            'city'         => 'Campinas',
            'neighborhood' => 'Industrial',
            'street'       => 'Avenida Brasil',
        ], 200),
    ]);

    Livewire::test(Form::class)
        ->set('form.address_zip_code', '13050000')
        ->call('buscarCep', app(BrasilAPIService::class))
        ->assertSet('form.address_street', 'Avenida Brasil')
        ->assertSet('form.address_city', 'Campinas')
        ->assertSet('form.address_state', 'SP');
});

it('salva novo fornecedor com sucesso', function () {
    Livewire::test(Form::class)
        ->set('form.social_name', 'Fornecedor Novo LTDA')
        ->set('form.name', 'Fornecedor Novo')
        ->set('form.taxNumber', '19131243000197')
        ->set('form.email', 'contato@fornecedor.com')
        ->set('form.phone_number', '11999998888')
        ->set('form.address_zip_code', '01310100')
        ->set('form.address_street', 'Av Paulista')
        ->set('form.address_number', '1000')
        ->set('form.address_district', 'Bela Vista')
        ->set('form.address_city', 'São Paulo')
        ->set('form.address_state', 'SP')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    expect(Supplier::where('email', 'contato@fornecedor.com')->exists())->toBeTrue();
});

it('lista fornecedores na pagina de index', function () {
    Supplier::factory()->create(['social_name' => 'Fornecedor Alpha', 'taxNumber' => '11111111111111']);
    Supplier::factory()->create(['social_name' => 'Fornecedor Beta',  'taxNumber' => '22222222222222']);

    Livewire::test(Index::class)
        ->assertSee('Fornecedor Alpha')
        ->assertSee('Fornecedor Beta');
});

it('filtra fornecedores pela busca', function () {
    Supplier::factory()->create(['social_name' => 'Distribuidora ABC', 'taxNumber' => '11111111111111', 'address_city' => 'Campinas']);
    Supplier::factory()->create(['social_name' => 'Distribuidora XYZ', 'taxNumber' => '22222222222222', 'address_city' => 'São Paulo']);

    Livewire::test(Index::class)
        ->set('search', 'ABC')
        ->assertSee('Distribuidora ABC')
        ->assertDontSee('Distribuidora XYZ');
});

