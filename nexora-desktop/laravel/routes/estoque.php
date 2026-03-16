<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\stockController;

Route::resource('stock', stockController::class);
