<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\productionOrdersController;

Route::resource('dashboard', dashboardController::class);
Route::resource('production_orders', productionOrdersController::class);
