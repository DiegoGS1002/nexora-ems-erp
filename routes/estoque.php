<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

Route::resource('stock', StockController::class);
