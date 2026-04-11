<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Rh\FolhaPagamento;
use App\Livewire\Rh\BatidaPonto;
use App\Http\Controllers\EmployeeManagementController;
use App\Http\Controllers\RhReportsController;
use App\Livewire\Rh\JornadaTrabalho;

Route::get('/rhReports/print', [RhReportsController::class, 'print'])->name('rhReports.print');

/* ─── Folha de Pagamento (Livewire) ─── */
Route::get('/payroll', FolhaPagamento::class)->name('payroll.index');

/* ─── Jornada de Trabalho (Livewire) ─── */
Route::get('/working_day', JornadaTrabalho::class)->name('working_day.index');

/* ─── Batida de Ponto (Livewire) ─── */
Route::get('/stitch_beat', BatidaPonto::class)->name('stitch_beat.index');

Route::resource('employee_management', EmployeeManagementController::class);
Route::resource('rh_reports', RhReportsController::class);
