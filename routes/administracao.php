<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConfigurationController;

Route::resource('profile', ProfileController::class);
Route::resource('configuration', ConfigurationController::class);
