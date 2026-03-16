<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\plansOfAccountsController;
use App\Http\Controllers\baccaratAccountsController;
use App\Http\Controllers\accountsPayableController;
use App\Http\Controllers\accountsReceivableController;
use App\Http\Controllers\cashFlowController;
use App\Http\Controllers\financialReportsController;

Route::get('/financialReports/print', [financialReportsController::class, 'print'])->name('financialReports.print');

Route::resource('plans_of_accounts', plansOfAccountsController::class);
Route::resource('baccarat_accounts', baccaratAccountsController::class);
Route::resource('accounts_payable', accountsPayableController::class);
Route::resource('accounts_receivable', accountsReceivableController::class);
Route::resource('cash_flow', cashFlowController::class);
Route::resource('financial_reports', financialReportsController::class);
