<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionOrdersController;

Route::resource('dashboard', DashboardController::class);
Route::resource('production_orders', ProductionOrdersController::class);
