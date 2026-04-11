<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Financeiro\PlanoContas;
use App\Livewire\Financeiro\ContaBancaria;
use App\Livewire\Financeiro\ContasPagar;
use App\Livewire\Financeiro\ContasReceber;
use App\Livewire\Financeiro\FluxoCaixa;
use App\Http\Controllers\BaccaratAccountsController;
use App\Http\Controllers\FinancialReportsController;

Route::get('/financialReports/print', [FinancialReportsController::class, 'print'])->name('financialReports.print');

/* ─── Plano de Contas (Livewire) ─── */
Route::get('/plans_of_accounts', PlanoContas::class)->name('plans_of_accounts.index');

/* ─── Contas Bancárias (Livewire) ─── */
Route::get('/contas-bancarias', ContaBancaria::class)->name('contas_bancarias.index');

/* ─── Contas a Pagar (Livewire) ─── */
Route::get('/accounts_payable', ContasPagar::class)->name('accounts_payable.index');

/* ─── Contas a Receber (Livewire) ─── */
Route::get('/accounts_receivable', ContasReceber::class)->name('accounts_receivable.index');

/* ─── Fluxo de Caixa (Livewire) ─── */
Route::get('/cash_flow', FluxoCaixa::class)->name('cash_flow.index');

/* ─── Demais recursos financeiros ─── */
Route::resource('baccarat_accounts', BaccaratAccountsController::class);
Route::resource('financial_reports', FinancialReportsController::class);


