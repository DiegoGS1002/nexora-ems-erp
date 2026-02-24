<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductSupplierController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\vehicleController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\productionOrdersController;
use App\Http\Controllers\stockController;
use App\Http\Controllers\requestsController;
use App\Http\Controllers\visitsController;
use App\Http\Controllers\salesReportController;
use App\Http\Controllers\entranceController;
use App\Http\Controllers\exitController;
use App\Http\Controllers\plansOfAccountsController;
use App\Http\Controllers\baccaratAccountsController;
use App\Http\Controllers\accountsPayableController;
use App\Http\Controllers\accountsReceivableController;
use App\Http\Controllers\cashFlowController;
use App\Http\Controllers\financialReportsController;
use App\Http\Controllers\workingDayController;
use App\Http\Controllers\stitchBeatController;
use App\Http\Controllers\payrollController;
use App\Http\Controllers\employeeManagementController;
use App\Http\Controllers\rhReportsController;
use App\Http\Controllers\routeManagementController;
use App\Http\Controllers\routingController;
use App\Http\Controllers\schedulingOfDeliveriesController;
use App\Http\Controllers\monitoringOfDeliveriesController;
use App\Http\Controllers\driverManagementController;
use App\Http\Controllers\romaneioController;
use App\Http\Controllers\vehicleTrackingController;
use App\Http\Controllers\vehicleMaintenanceController;
use App\Http\Controllers\transportReportController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\ConfigurationController;

Route::get('/', function () {
    return view('home-page');
})->name('home');

// Print/list export routes (moved before resource routes to avoid conflict with resource show routes)
Route::get('/clients/print', [ClientController::class, 'print'])->name('clients.print');
Route::get('/products/print', [ProductController::class, 'print'])->name('products.print');
Route::get('/suppliers/print', [SupplierController::class, 'print'])->name('suppliers.print');
Route::get('/transportReport/print', [transportReportController::class, 'print'])->name('transportReport.print');
Route::get('/romaneio/print', [romaneioController::class, 'print'])->name('romaneio.print');
Route::get('/rhReports/print', [rhReportsController::class, 'print'])->name('rhReports.print');
Route::get('/financialReports/print', [financialReportsController::class, 'print'])->name('financialReports.print');
Route::get('/salesReports/print', [salesReportController::class, 'print'])->name('salesReports.print');

//Cadastro
Route::resource('clients', ClientController::class);
Route::resource('products', ProductController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('employee', employeeController::class);
Route::resource('role', roleController::class);
Route::resource('vehicle', vehicleController::class);

//Produção
Route::resource('dashboard', dashboardController::class);
Route::resource('production_orders', productionOrdersController::class);
Route::resource('stock', stockController::class);

//Vendas
Route::resource('requests', requestsController::class);
Route::resource('visits', visitsController::class);
Route::resource('sales_report', salesReportController::class);

//Fiscal
Route::resource('entrance', entranceController::class);
Route::resource('exit', exitController::class);

//Financeiro
Route::resource('plans_of_accounts', plansOfAccountsController::class);
Route::resource('baccarat_accounts', baccaratAccountsController::class);
Route::resource('accounts_payable', accountsPayableController::class);
Route::resource('accounts_receivable', accountsReceivableController::class);
Route::resource('cash_flow', cashFlowController::class);
Route::resource('financial_reports', financialReportsController::class);

//RH
Route::resource('working_day', workingDayController::class);
Route::resource('stitch_beat', stitchBeatController::class);
Route::resource('payroll', payrollController::class);
Route::resource('employee_management', employeeManagementController::class);
Route::resource('rh_reports', rhReportsController::class);

//Transporte
Route::resource('route_management', routeManagementController::class);
Route::resource('routing', routingController::class);
Route::resource('scheduling_of_deliveries', schedulingOfDeliveriesController::class);
Route::resource('monitoring_of_deliveries', monitoringOfDeliveriesController::class);
Route::resource('driver_management', driverManagementController::class);
Route::resource('romaneio', romaneioController::class);
Route::resource('vehicle_tracking', vehicleTrackingController::class);
Route::resource('vehicle_maintenance', vehicleMaintenanceController::class);
Route::resource('transport_report', transportReportController::class);

//perfil
Route::resource('profile', profileController::class);
Route::resource('configuration', configurationController::class);

Route::get( '/products/{product}/suppliers', [ProductSupplierController::class, 'index'])->name('products.suppliers.index');

Route::post( '/products/{product}/suppliers', [ProductSupplierController::class, 'store'])->name('products.suppliers.store');

Route::delete( '/products/{product}/suppliers/{supplier}', [ProductSupplierController::class, 'destroy'])->name('products.suppliers.destroy');
