<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\vehicleController;
use App\Http\Controllers\ProductSupplierController;

/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

Route::get('/clients/print', [ClientController::class, 'print'])->name('clients.print');
Route::get('/products/print', [ProductController::class, 'print'])->name('products.print');
Route::get('/suppliers/print', [SupplierController::class, 'print'])->name('suppliers.print');
Route::get('/employees/print', [
    App\Http\Controllers\EmployeeController::class,
    'print'
])->name('employees.print');
Route::get('/vehicles/print', [
    App\Http\Controllers\VehicleController::class,
    'print'
])->name('vehicles.print');


/*
|--------------------------------------------------------------------------
| RESOURCES
|--------------------------------------------------------------------------
*/

Route::resource('clients', ClientController::class);
Route::resource('products', ProductController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('employees', employeeController::class);
Route::resource('role', roleController::class);
Route::resource('vehicles', vehicleController::class);


/*
|--------------------------------------------------------------------------
| PRODUCT SUPPLIERS
|--------------------------------------------------------------------------
*/

Route::get('/products/{product}/suppliers', [ProductSupplierController::class, 'index'])->name('products.suppliers.index');
Route::post('/products/{product}/suppliers', [ProductSupplierController::class, 'store'])->name('products.suppliers.store');
Route::delete('/products/{product}/suppliers/{supplier}', [ProductSupplierController::class, 'destroy'])->name('products.suppliers.destroy');
