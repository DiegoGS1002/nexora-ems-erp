<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\ProductSupplierApiController;
use App\Http\Controllers\Api\ClientApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Products
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::put('/products/{product}', [ProductApiController::class, 'update']);
    Route::patch('/products/{product}', [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);

    // Suppliers
    Route::get('/suppliers', [SupplierApiController::class, 'index']);
    Route::post('/suppliers', [SupplierApiController::class, 'store']);
    Route::get('/suppliers/{supplier}', [SupplierApiController::class, 'show']);
    Route::put('/suppliers/{supplier}', [SupplierApiController::class, 'update']);
    Route::patch('/suppliers/{supplier}', [SupplierApiController::class, 'update']);
    Route::delete('/suppliers/{supplier}', [SupplierApiController::class, 'destroy']);

    // Clients
    Route::get('/clients', [ClientApiController::class, 'index']);
    Route::post('/clients', [ClientApiController::class, 'store']);
    Route::get('/clients/{client}', [ClientApiController::class, 'show']);
    Route::put('/clients/{client}', [ClientApiController::class, 'update']);
    Route::patch('/clients/{client}', [ClientApiController::class, 'update']);
    Route::delete('/clients/{client}', [ClientApiController::class, 'destroy']);

    // Product x Supplier
    Route::get('/products/{product}/suppliers', [ProductSupplierApiController::class, 'index']);
    Route::post('/products/{product}/suppliers', [ProductSupplierApiController::class, 'attach']);
    Route::delete('/products/{product}/suppliers/{supplier}', [ProductSupplierApiController::class, 'detach']);
});

