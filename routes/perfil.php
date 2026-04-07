<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;
use App\Livewire\Administracao\Logs\Index as LogsIndex;
use App\Livewire\Administracao\Notifications\Index as NotificationsIndex;

Route::middleware('admin')->group(function () {
	Route::resource('users', UsersController::class)
		->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

	Route::resource('permissions', PermissionsController::class);

	Route::get('/logs', LogsIndex::class)->name('logs.index');
});

// ─── Notificações do Usuário ──────────────────────────────────────────
Route::get('/notificacoes', NotificationsIndex::class)->name('notifications.index');

// ─── Perfil do Usuário ───────────────────────────────────────────
Route::prefix('perfil')->name('profile.')->group(function () {
    Route::get('/',                [ProfileController::class, 'index'])->name('index');
    Route::patch('/info',          [ProfileController::class, 'updateInfo'])->name('updateInfo');
    Route::patch('/senha',         [ProfileController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/avatar',         [ProfileController::class, 'uploadAvatar'])->name('uploadAvatar');
    Route::delete('/avatar',       [ProfileController::class, 'removeAvatar'])->name('removeAvatar');
});
