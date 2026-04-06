<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MaintenanceERP;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\ModulePageController;
use App\Livewire\Suporte\Chat as ChatSuporte;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'midnight.session', MaintenanceERP::class])->group(function () {

    Route::get('/', function () {
        return view('home-page', [
            'modules' => ModulePageController::allModules(),
        ]);
    })->name('home');

    Route::get('/modulo/{module}/item/{item}', [ModulePageController::class, 'featureDevelopment'])
        ->name('module.item.development');

    Route::get('/modulo/{module}', [ModulePageController::class, 'show'])->name('module.show');

    Route::get('/suporte/chat', ChatSuporte::class)->name('suporte.chat');

/*
|--------------------------------------------------------------------------
| PRINT ROUTES (ficam aqui para evitar conflito)
|--------------------------------------------------------------------------
*/

require __DIR__.'/administracao.php';
require __DIR__.'/cadastro.php';
require __DIR__.'/compras.php';
require __DIR__.'/producao.php';
require __DIR__.'/vendas.php';
require __DIR__.'/fiscal.php';
require __DIR__.'/financeiro.php';
require __DIR__.'/rh.php';
require __DIR__.'/logistica.php';
require __DIR__.'/estoque.php';
require __DIR__.'/perfil.php';

});
