<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MaintenanceERP;
use App\Http\Controllers\ModulePageController;

Route::middleware([MaintenanceERP::class])->group(function () {

    Route::get('/', function () {
        return view('home-page', [
            'modules' => ModulePageController::allModules(),
        ]);
    })->name('home');

    Route::get('/modulo/{module}', [ModulePageController::class, 'show'])->name('module.show');

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
