<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\workingDayController;
use App\Http\Controllers\stitchBeatController;
use App\Http\Controllers\payrollController;
use App\Http\Controllers\employeeManagementController;
use App\Http\Controllers\rhReportsController;

Route::get('/rhReports/print', [rhReportsController::class, 'print'])->name('rhReports.print');

Route::resource('working_day', workingDayController::class);
Route::resource('stitch_beat', stitchBeatController::class);
Route::resource('payroll', payrollController::class);
Route::resource('employee_management', employeeManagementController::class);
Route::resource('rh_reports', rhReportsController::class);
