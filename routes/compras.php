<?php

use App\Livewire\Compras\Cotacoes;
use App\Livewire\Compras\PedidosCompra;
use App\Livewire\Compras\SolicitacoesCompra;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| COMPRAS — Rotas do módulo de Compras
|--------------------------------------------------------------------------
*/

Route::get('/compras/solicitacoes', SolicitacoesCompra::class)->name('compras.solicitacoes');

/* ─── Pedidos de Compra (Livewire) ─── */
Route::get('/compras/pedidos', PedidosCompra::class)->name('compras.pedidos');

Route::get('/compras/cotacoes', Cotacoes::class)->name('compras.cotacoes');

