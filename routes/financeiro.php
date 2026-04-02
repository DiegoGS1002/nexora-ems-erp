<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlansOfAccountsController;
use App\Http\Controllers\BaccaratAccountsController;
use App\Http\Controllers\AccountsPayableController;
use App\Http\Controllers\AccountsReceivableController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\FinancialReportsController;

Route::get('/financialReports/print', [FinancialReportsController::class, 'print'])->name('financialReports.print');

Route::resource('plans_of_accounts', PlansOfAccountsController::class);
Route::resource('baccarat_accounts', BaccaratAccountsController::class);
Route::resource('accounts_payable', AccountsPayableController::class);
Route::resource('accounts_receivable', AccountsReceivableController::class);
Route::resource('cash_flow', CashFlowController::class);
Route::resource('financial_reports', FinancialReportsController::class);
