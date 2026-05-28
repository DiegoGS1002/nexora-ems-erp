<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Financeiro\PlanoContas;
use App\Livewire\Financeiro\ContaBancaria;
use App\Livewire\Financeiro\ContasPagar;
use App\Livewire\Financeiro\ContasReceber;
use App\Livewire\Financeiro\FluxoCaixa;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\FinancialReportsController;

Route::get('/financialReports/print', [FinancialReportsController::class, 'print'])->name('financialReports.print');

Route::get('/plans_of_accounts', PlanoContas::class)->name('plans_of_accounts.index');
Route::get('/contas-bancarias', ContaBancaria::class)->name('contas_bancarias.index');
Route::get('/accounts_payable', ContasPagar::class)->name('accounts_payable.index');
Route::get('/accounts_receivable', ContasReceber::class)->name('accounts_receivable.index');
Route::get('/cash_flow', FluxoCaixa::class)->name('cash_flow.index');

Route::resource('bank_accounts', BankAccountController::class);
Route::resource('financial_reports', FinancialReportsController::class);
