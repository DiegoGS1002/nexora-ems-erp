<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntranceController;
use App\Http\Controllers\ExitController;
use App\Livewire\Fiscal\NotaFiscal;
use App\Livewire\Fiscal\TipoOperacao\Index as TipoOperacaoIndex;
use App\Livewire\Fiscal\TipoOperacao\Form as TipoOperacaoForm;
use App\Livewire\Fiscal\GrupoTributario\Index as GrupoTributarioIndex;
use App\Livewire\Fiscal\GrupoTributario\Form as GrupoTributarioForm;

/* ─── Notas Fiscais Eletrônicas (Livewire) ─── */
Route::get('/fiscal/notas-fiscais', NotaFiscal::class)->name('fiscal.nfe.index');

/* ─── Tipos de Operação Fiscal (Livewire) ─── */
Route::get('/fiscal/tipos-operacao', TipoOperacaoIndex::class)->name('fiscal.tipo-operacao.index');
Route::get('/fiscal/tipos-operacao/create', TipoOperacaoForm::class)->name('fiscal.tipo-operacao.create');
Route::get('/fiscal/tipos-operacao/{operacao}/edit', TipoOperacaoForm::class)->name('fiscal.tipo-operacao.edit');

/* ─── Grupos Tributários (Livewire) ─── */
Route::get('/fiscal/grupos-tributarios', GrupoTributarioIndex::class)->name('fiscal.grupo-tributario.index');
Route::get('/fiscal/grupos-tributarios/create', GrupoTributarioForm::class)->name('fiscal.grupo-tributario.create');
Route::get('/fiscal/grupos-tributarios/{grupo}/edit', GrupoTributarioForm::class)->name('fiscal.grupo-tributario.edit');

Route::resource('/fiscal/entrada', EntranceController::class)->names('fiscal.entrance');
Route::resource('/fiscal/saida', ExitController::class)->names('fiscal.exit');
