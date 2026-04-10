<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\ProductSupplierApiController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\StockMovementApiController;
use App\Http\Controllers\Api\AccountsPayableApiController;
use App\Http\Controllers\Api\AccountsReceivableApiController;
use App\Http\Controllers\ExternalApiProxyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/proxy/cnpj/{cnpj}', [ExternalApiProxyController::class, 'getCnpj']);
    Route::get('/proxy/cep/{cep}', [ExternalApiProxyController::class, 'getCep']);
});

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

    // Stock Movements
    Route::get('/stock-movements', [StockMovementApiController::class, 'index']);
    Route::post('/stock-movements', [StockMovementApiController::class, 'store']);
    Route::get('/stock-movements/{stockMovement}', [StockMovementApiController::class, 'show']);
    Route::put('/stock-movements/{stockMovement}', [StockMovementApiController::class, 'update']);
    Route::patch('/stock-movements/{stockMovement}', [StockMovementApiController::class, 'update']);
    Route::delete('/stock-movements/{stockMovement}', [StockMovementApiController::class, 'destroy']);

    // Accounts Payable
    Route::get('/accounts-payable', [AccountsPayableApiController::class, 'index']);
    Route::post('/accounts-payable', [AccountsPayableApiController::class, 'store']);
    Route::get('/accounts-payable/{accountPayable}', [AccountsPayableApiController::class, 'show']);
    Route::put('/accounts-payable/{accountPayable}', [AccountsPayableApiController::class, 'update']);
    Route::patch('/accounts-payable/{accountPayable}', [AccountsPayableApiController::class, 'update']);
    Route::delete('/accounts-payable/{accountPayable}', [AccountsPayableApiController::class, 'destroy']);

    // Accounts Receivable
    Route::get('/accounts-receivable', [AccountsReceivableApiController::class, 'index']);
    Route::post('/accounts-receivable', [AccountsReceivableApiController::class, 'store']);
    Route::get('/accounts-receivable/{accountReceivable}', [AccountsReceivableApiController::class, 'show']);
    Route::put('/accounts-receivable/{accountReceivable}', [AccountsReceivableApiController::class, 'update']);
    Route::patch('/accounts-receivable/{accountReceivable}', [AccountsReceivableApiController::class, 'update']);
    Route::delete('/accounts-receivable/{accountReceivable}', [AccountsReceivableApiController::class, 'destroy']);
});




