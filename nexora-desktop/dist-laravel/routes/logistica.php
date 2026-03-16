<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\routeManagementController;
use App\Http\Controllers\routingController;
use App\Http\Controllers\schedulingOfDeliveriesController;
use App\Http\Controllers\monitoringOfDeliveriesController;
use App\Http\Controllers\driverManagementController;
use App\Http\Controllers\romaneioController;
use App\Http\Controllers\vehicleTrackingController;
use App\Http\Controllers\vehicleMaintenanceController;
use App\Http\Controllers\transportReportController;

Route::get('/transportReport/print', [transportReportController::class, 'print'])->name('transportReport.print');
Route::get('/romaneio/print', [romaneioController::class, 'print'])->name('romaneio.print');

Route::resource('route_management', routeManagementController::class);
Route::resource('routing', routingController::class);
Route::resource('scheduling_of_deliveries', schedulingOfDeliveriesController::class);
Route::resource('monitoring_of_deliveries', monitoringOfDeliveriesController::class);
Route::resource('driver_management', driverManagementController::class);
Route::resource('romaneio', romaneioController::class);
Route::resource('vehicle_tracking', vehicleTrackingController::class);
Route::resource('vehicle_maintenance', vehicleMaintenanceController::class);
Route::resource('transport_report', transportReportController::class);
