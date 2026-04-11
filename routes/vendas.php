<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\VisitsController;
use App\Http\Controllers\SalesReportController;
use App\Livewire\Vendas\TabelasPrecificacao;

Route::get('/salesReports/print', [SalesReportController::class, 'print'])->name('salesReports.print');

// ── Pedidos de Venda ─────────────────────────────────────────────────────────
Route::get('/vendas/pedidos', fn () => view('vendas.pedidos'))->name('vendas.pedidos');

// ── Tabelas de Precificação ──────────────────────────────────────────────────
Route::get('/vendas/precificacao', TabelasPrecificacao::class)->name('vendas.precificacao');

Route::resource('requests', RequestsController::class);
Route::resource('visits', VisitsController::class);
Route::resource('sales_report', SalesReportController::class);
