<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Financeiro\PlanoContas;
use App\Livewire\Financeiro\ContaBancaria;
use App\Http\Controllers\BaccaratAccountsController;
use App\Http\Controllers\AccountsPayableController;
use App\Http\Controllers\AccountsReceivableController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\FinancialReportsController;

Route::get('/financialReports/print', [FinancialReportsController::class, 'print'])->name('financialReports.print');

/* ─── Plano de Contas (Livewire) ─── */
Route::get('/plans_of_accounts', PlanoContas::class)->name('plans_of_accounts.index');

/* ─── Contas Bancárias (Livewire) ─── */
Route::get('/contas-bancarias', ContaBancaria::class)->name('contas_bancarias.index');

/* ─── Demais recursos financeiros ─── */
Route::resource('baccarat_accounts', BaccaratAccountsController::class);
Route::resource('accounts_payable', AccountsPayableController::class);
Route::resource('accounts_receivable', AccountsReceivableController::class);
Route::resource('cash_flow', CashFlowController::class);
Route::resource('financial_reports', FinancialReportsController::class);


