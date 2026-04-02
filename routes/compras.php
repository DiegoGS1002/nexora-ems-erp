<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| COMPRAS — Rotas do módulo de Compras
|--------------------------------------------------------------------------
*/

Route::get('/compras/solicitacoes', function () {
    return view('system.desenvolvimento', [
        'title'       => 'Solicitações de Compra',
        'description' => 'Solicitações internas de aquisição de materiais e serviços',
        'color'       => '#F97316',
        'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>',
        'moduleSlug'  => 'compras',
        'moduleName'  => 'Compras',
    ]);
})->name('compras.solicitacoes');

Route::get('/compras/pedidos', function () {
    return view('system.desenvolvimento', [
        'title'       => 'Pedidos de Compra',
        'description' => 'Ordens de compra emitidas para fornecedores',
        'color'       => '#F97316',
        'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
        'moduleSlug'  => 'compras',
        'moduleName'  => 'Compras',
    ]);
})->name('compras.pedidos');

Route::get('/compras/cotacoes', function () {
    return view('system.desenvolvimento', [
        'title'       => 'Cotações',
        'description' => 'Comparação de preços e seleção de fornecedores',
        'color'       => '#F97316',
        'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
        'moduleSlug'  => 'compras',
        'moduleName'  => 'Compras',
    ]);
})->name('compras.cotacoes');

