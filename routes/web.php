<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SupplierPurchaseOrderController;



Route::resource('suppliers/purchase-orders', SupplierPurchaseOrderController::class);

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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/roles', function () {
        return Inertia::render('Roles/Index');
    })->name('roles.index');
});

Route::middleware(['auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('catalogo')->group(function () {
    Route::get('/prestaciones', [\App\Http\Controllers\BenefitController::class, 'index'])
        ->name('catalogo.prestaciones.index');

    Route::post('/prestaciones', [\App\Http\Controllers\BenefitController::class, 'store'])
        ->name('catalogo.prestaciones.store');

});