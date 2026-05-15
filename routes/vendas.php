<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\VisitsController;
use App\Http\Controllers\SalesReportController;
use App\Livewire\Vendas\TabelasPrecificacao;
use App\Livewire\Vendas\PedidosVenda;

Route::get('/salesReports/print', [SalesReportController::class, 'print'])->name('salesReports.print');

use App\Livewire\Vendas\PedidoForm;

// ── Pedidos de Venda ─────────────────────────────────────────────────────────
Route::get('/vendas/pedidos', PedidosVenda::class)->name('vendas.pedidos');
Route::get('/vendas/pedidos/novo', PedidoForm::class)->name('vendas.pedidos.novo');
Route::get('/vendas/pedidos/{orderId}/editar', PedidoForm::class)->name('vendas.pedidos.editar');

// ── Tabelas de Precificação ──────────────────────────────────────────────────
Route::get('/vendas/precificacao', TabelasPrecificacao::class)->name('vendas.precificacao');

Route::resource('requests', RequestsController::class);
Route::resource('visits', VisitsController::class);
Route::resource('sales_report', SalesReportController::class);
