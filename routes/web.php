<?php

use App\Http\Controllers\RestletController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SupplierPurchaseOrderController;
use App\Http\Controllers\SuplierUserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierInvoicesController;
use App\Http\Controllers\SupplierWhatsappController;
use App\Http\Controllers\SupplierPurchaseOrderReceiptController;
use App\Http\Controllers\DashboardController;




Route::prefix('suppliers')->group(function () {
    Route::resource('purchase-orders', SupplierPurchaseOrderController::class);
    Route::resource('purchase-orders-invoices', SupplierInvoicesController::class);
    Route::resource('purchase-orders-receipt', SupplierPurchaseOrderReceiptController::class);
    //Route::resource('supplier', SupplierController::class);
});


Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {return Inertia::render('Dashboard');})->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route::get('/users', [SuplierUserController::class, 'index'])->name('users.index');
    // Route::post('/users', [SuplierUserController::class, 'store'])->name('users.store');
    // Route::delete('/users/{id}', [SuplierUserController::class, 'destroy'])->name('users.destroy');
    // Route::put('/users/{id}/update', [SuplierUserController::class, 'update'])->name('users.update');
    // Route::delete('/users', [SuplierUserController::class, 'destroySelected'])->name('users.destroySelected');

    Route::resource('supplier-whatsapp', SupplierWhatsappController::class)->only(['index']);
});

Route::get('/netsuite/restlet/{scriptId}/{deployId}', [RestletController::class, 'getRestletResponse']);
Route::post('/netsuite/restlet/{scriptId}/{deployId}', [RestletController::class, 'postRestletResponse']);

// //Route::post('/purchase-orders', [SupplierPurchaseOrderController::class, 'storePurchaseOrder'])->name('purchase-orders.store');
