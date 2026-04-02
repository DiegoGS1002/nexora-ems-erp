<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\VisitsController;
use App\Http\Controllers\SalesReportController;

Route::get('/salesReports/print', [SalesReportController::class, 'print'])->name('salesReports.print');

Route::resource('requests', RequestsController::class);
Route::resource('visits', VisitsController::class);
Route::resource('sales_report', SalesReportController::class);
