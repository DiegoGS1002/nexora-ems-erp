<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConfigurationController;

Route::resource('profile', ProfileController::class);

Route::get('/configuracoes',  [ConfigurationController::class, 'index'])->name('configuration.index');
Route::post('/configuracoes', [ConfigurationController::class, 'store'])->name('configuration.store');

