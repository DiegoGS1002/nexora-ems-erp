<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigurationController;

Route::get('/configuracoes',  [ConfigurationController::class, 'index'])->name('configuration.index');
Route::post('/configuracoes', [ConfigurationController::class, 'store'])->name('configuration.store');

