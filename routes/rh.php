<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkingDayController;
use App\Http\Controllers\StitchBeatController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeManagementController;
use App\Http\Controllers\RhReportsController;

Route::get('/rhReports/print', [RhReportsController::class, 'print'])->name('rhReports.print');

Route::resource('working_day', WorkingDayController::class);
Route::resource('stitch_beat', StitchBeatController::class);
Route::resource('payroll', PayrollController::class);
Route::resource('employee_management', EmployeeManagementController::class);
Route::resource('rh_reports', RhReportsController::class);
