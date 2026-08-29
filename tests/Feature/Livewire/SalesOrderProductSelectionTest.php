<?php

use App\Livewire\Vendas\PedidoForm;
use App\Livewire\Vendas\PedidosVenda;
use App\Models\Product;
use Livewire\Livewire;

it('lists active products and adds one in the sales order form', function () {
    $activeProduct = Product::factory()->create(['name' => 'Produto Disponível']);
    Product::factory()->inactive()->create(['name' => 'Produto Inativo']);

    Livewire::test(PedidoForm::class)
        ->call('showAvailableProducts')
        ->assertSet('searchResults.0.id', $activeProduct->id)
        ->call('addProduct', $activeProduct->id)
        ->assertSet('orderItems.0.product_id', $activeProduct->id)
        ->assertSet('orderItems.0.product_name', 'Produto Disponível');
});

it('lists active products and adds one in the sales orders modal', function () {
    $activeProduct = Product::factory()->create(['name' => 'Produto para Modal']);
    Product::factory()->inactive()->create(['name' => 'Produto Inativo']);

    Livewire::test(PedidosVenda::class)
        ->call('showAvailableProducts')
        ->assertSet('searchResults.0.id', $activeProduct->id)
        ->call('addProduct', $activeProduct->id)
        ->assertSet('orderItems.0.product_id', $activeProduct->id)
        ->assertSet('orderItems.0.product_name', 'Produto para Modal');
});
