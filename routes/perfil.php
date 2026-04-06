<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;
use App\Livewire\Administracao\Logs\Index as LogsIndex;

Route::middleware('admin')->group(function () {
	Route::resource('users', UsersController::class)
		->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

	Route::resource('permissions', PermissionsController::class);

	Route::get('/logs', LogsIndex::class)->name('logs.index');
});

