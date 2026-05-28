<?php

use App\Livewire\Compras\Cotacoes;
use App\Livewire\Compras\PedidosCompra;
use App\Livewire\Compras\SolicitacoesCompra;
use Illuminate\Support\Facades\Route;

Route::get('/compras/solicitacoes', SolicitacoesCompra::class)->name('compras.solicitacoes');
Route::get('/compras/pedidos', PedidosCompra::class)->name('compras.pedidos');
Route::get('/compras/cotacoes', Cotacoes::class)->name('compras.cotacoes');
