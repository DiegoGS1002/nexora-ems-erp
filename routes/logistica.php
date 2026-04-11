<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteManagementController;
use App\Http\Controllers\RoutingController;
use App\Livewire\Logistica\AgendamentoEntregas;
use App\Http\Controllers\MonitoringOfDeliveriesController;
use App\Http\Controllers\DriverManagementController;
use App\Http\Controllers\RomaneioController;
use App\Http\Controllers\VehicleTrackingController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\TransportReportController;

Route::get('/transportReport/print', [TransportReportController::class, 'print'])->name('transportReport.print');
Route::get('/romaneio/print', [RomaneioController::class, 'print'])->name('romaneio.print');

Route::resource('route_management', RouteManagementController::class);
Route::resource('routing', RoutingController::class);
Route::get('/logistica/agendamento-entregas', AgendamentoEntregas::class)->name('scheduling_of_deliveries.index');
Route::resource('monitoring_of_deliveries', MonitoringOfDeliveriesController::class);
Route::resource('driver_management', DriverManagementController::class);
Route::resource('romaneio', RomaneioController::class);
Route::resource('vehicle_tracking', VehicleTrackingController::class);
Route::resource('vehicle_maintenance', VehicleMaintenanceController::class);
Route::resource('transport_report', TransportReportController::class);
