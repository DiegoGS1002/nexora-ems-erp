<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigurationController;
use App\Livewire\Administracao\Empresas\Index as EmpresasIndex;
use App\Livewire\Administracao\Empresas\Form as EmpresasForm;

Route::get('/configuracoes',  [ConfigurationController::class, 'index'])->name('configuration.index');
Route::post('/configuracoes', [ConfigurationController::class, 'store'])->name('configuration.store');

/*
|--------------------------------------------------------------------------
| EMPRESAS — somente administradores
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {
    Route::get('/empresas', EmpresasIndex::class)->name('companies.index');
    Route::get('/empresas/create', EmpresasForm::class)->name('companies.create');
    Route::get('/empresas/{company}/edit', EmpresasForm::class)->name('companies.edit');
});

