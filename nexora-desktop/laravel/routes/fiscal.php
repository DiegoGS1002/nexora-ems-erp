<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\entranceController;
use App\Http\Controllers\exitController;

Route::resource('entrance', entranceController::class);
Route::resource('exit', exitController::class);
