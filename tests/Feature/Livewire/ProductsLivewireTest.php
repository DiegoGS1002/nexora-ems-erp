<?php

use App\Livewire\Cadastro\Produtos\Form as ProductForm;
use App\Livewire\Cadastro\Produtos\Index as ProductIndex;
use App\Models\Product;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('creates a product through livewire form', function () {
    Livewire::test(ProductForm::class)
        ->set('form.name', 'Produto Teste')
        ->set('form.ean', '7891234567890')
        ->set('form.description', 'Descricao do produto')
        ->set('form.unit_of_measure', 'UN')
        ->set('form.sale_price', '10.90')
        ->set('form.stock', '12')
        ->set('form.category', 'outro')
        ->call('save');

    $this->assertDatabaseHas('products', [
        'name' => 'Produto Teste',
        'ean'  => '7891234567890',
    ]);
});

it('updates an existing product through livewire form', function () {
    $product = Product::factory()->create([
        'name' => 'Produto Antigo',
        'ean'  => '7891234567891',
    ]);

    Livewire::test(ProductForm::class, ['product' => $product])
        ->set('form.name', 'Produto Novo')
        ->set('form.ean', '7891234567892')
        ->set('form.category', 'outro')
        ->call('save')
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'id'   => $product->id,
        'name' => 'Produto Novo',
        'ean'  => '7891234567892',
    ]);
});

it('deletes a product from livewire index', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductIndex::class)
        ->call('deleteProduct', $product->id);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});
