<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\requestsController;
use App\Http\Controllers\visitsController;
use App\Http\Controllers\salesReportController;

Route::get('/salesReports/print', [salesReportController::class, 'print'])->name('salesReports.print');

Route::resource('requests', requestsController::class);
Route::resource('visits', visitsController::class);
Route::resource('sales_report', salesReportController::class);
