<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\LogsController;

Route::resource('users', UsersController::class);
Route::resource('permissions', PermissionsController::class);
Route::resource('logs', LogsController::class);

