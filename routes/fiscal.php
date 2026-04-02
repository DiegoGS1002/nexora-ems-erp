<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntranceController;
use App\Http\Controllers\ExitController;

Route::resource('entrance', EntranceController::class);
Route::resource('exit', ExitController::class);
