<?php

use App\Enums\NaturezaProduto;
use App\Enums\TipoProduto;
use App\Livewire\Cadastro\Produtos\Form;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

// ══════════════════════════════════════════════════
// RENDERIZAÇÃO
// ══════════════════════════════════════════════════

it('renderiza a página de criação com as abas e campos esperados', function () {
    Livewire::test(Form::class)
        ->assertSee('Novo Produto')
        ->assertSee('Dados Gerais')
        ->assertSee('Preços e Custos')
        ->assertSee('Estoque')
        ->assertSee('Fornecedores')
        ->assertSee('Informações Básicas')
        ->assertSee('Dimensões e Peso');
});

it('renderiza a página de edição com o badge Editando', function () {
    $product = Product::factory()->create();

    Livewire::test(Form::class, ['product' => $product])
        ->assertSee('Editar Produto')
        ->assertSee('Editando')
        ->assertSet('form.name', $product->name);
});

// ══════════════════════════════════════════════════
// VALIDAÇÃO
// ══════════════════════════════════════════════════

it('valida campos obrigatórios ao tentar salvar vazio', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors([
            'form.name',
            'form.category',
        ]);
});

it('valida que o EAN deve ter exatamente 13 dígitos', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Teste')
        ->set('form.ean', '123')
        ->set('form.category', 'informatica')
        ->call('save')
        ->assertHasErrors(['form.ean']);
});

it('aceita EAN nulo (campo opcional)', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Sem EAN')
        ->set('form.ean', null)
        ->set('form.category', 'informatica')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->call('save')
        ->assertHasNoErrors(['form.ean']);
});

// ══════════════════════════════════════════════════
// CRIAÇÃO
// ══════════════════════════════════════════════════

it('cria um produto com todos os campos obrigatórios preenchidos', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Monitor LED 24"')
        ->set('form.ean', '7891234567890')
        ->set('form.category', 'informatica')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('products.index'));

    expect(Product::where('name', 'Monitor LED 24"')->exists())->toBeTrue();
});

it('gera product_code automaticamente ao criar produto', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Auto Código')
        ->set('form.category', 'outro')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'Produto Auto Código')->first();
    expect($product)->not->toBeNull();
    expect($product->product_code)->toStartWith('PROD-');
});

it('salva destaques e tags corretamente', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto com Tags')
        ->set('form.category', 'outro')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->set('form.highlights', ['Full HD', 'HDMI'])
        ->set('form.tags', ['monitor', 'led'])
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'Produto com Tags')->first();
    expect($product->highlights)->toContain('Full HD');
    expect($product->tags)->toContain('monitor');
});

it('salva e reseta o formulário ao chamar saveAndNew', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Rápido')
        ->set('form.category', 'outro')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->call('saveAndNew')
        ->assertHasNoErrors()
        ->assertSet('form.name', '');

    expect(Product::where('name', 'Produto Rápido')->exists())->toBeTrue();
});

// ══════════════════════════════════════════════════
// EDIÇÃO
// ══════════════════════════════════════════════════

it('atualiza um produto existente corretamente', function () {
    $product = Product::factory()->create(['name' => 'Produto Original']);

    Livewire::test(Form::class, ['product' => $product])
        ->set('form.name', 'Produto Atualizado')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('products.index'));

    expect(Product::find($product->id)->name)->toBe('Produto Atualizado');
});

it('mantém o produto_code original ao editar', function () {
    $product = Product::factory()->create();
    $originalCode = $product->product_code;

    Livewire::test(Form::class, ['product' => $product])
        ->set('form.name', 'Nome Novo')
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::find($product->id)->product_code)->toBe($originalCode);
});

// ══════════════════════════════════════════════════
// GERENCIAMENTO DE HIGHLIGHTS/TAGS
// ══════════════════════════════════════════════════

it('adiciona e remove destaques via métodos do componente', function () {
    $component = Livewire::test(Form::class)
        ->set('highlightInput', 'Tela Full HD')
        ->call('addHighlight')
        ->assertSet('highlightInput', '')
        ->assertSet('form.highlights', ['Tela Full HD']);

    $component->call('removeHighlight', 0)
        ->assertSet('form.highlights', []);
});

it('adiciona e remove tags via métodos do componente', function () {
    $component = Livewire::test(Form::class)
        ->set('tagInput', 'Monitor LED')
        ->call('addTag')
        ->assertSet('tagInput', '')
        ->assertSet('form.tags', ['monitor led']);

    $component->call('removeTag', 0)
        ->assertSet('form.tags', []);
});

it('não adiciona destaque duplicado', function () {
    Livewire::test(Form::class)
        ->set('form.highlights', ['Full HD'])
        ->set('highlightInput', 'Full HD')
        ->call('addHighlight')
        ->assertSet('form.highlights', ['Full HD']);
});

// ══════════════════════════════════════════════════
// STATUS
// ══════════════════════════════════════════════════

it('cria produto ativo por padrão', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Ativo')
        ->set('form.category', 'outro')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->call('save');

    expect(Product::where('name', 'Produto Ativo')->first()->is_active)->toBeTrue();
});

it('cria produto como inativo quando toggle está desligado', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Produto Inativo')
        ->set('form.category', 'outro')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.product_type', TipoProduto::Fisico->value)
        ->set('form.nature', NaturezaProduto::MercadoriaRevenda->value)
        ->set('form.is_active', false)
        ->call('save');

    expect(Product::where('name', 'Produto Inativo')->first()->is_active)->toBeFalse();
});


