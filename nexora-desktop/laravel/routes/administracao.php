<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\profileController;
use App\Http\Controllers\ConfigurationController;

Route::resource('profile', profileController::class);
Route::resource('configuration', configurationController::class);
