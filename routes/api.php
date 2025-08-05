<?php

use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPurchaseOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwilioController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/purchase-orders', [SupplierPurchaseOrderController::class, 'storePurchaseOrder'])->name('purchase-orders.store');
Route::middleware('auth:sanctum')->post('/suppliers/create', [SupplierController::class, 'storeSupliers'])->name('suppliers.storeSupliers');
//Route::post('/purchase-orders', [SupplierPurchaseOrderController::class, 'storePurchaseOrder'])->name('purchase-orders.store');
Route::middleware('auth:sanctum')->post('/send-whatsapp', [TwilioController::class, 'sendWhatsApp'])->name('send-whatsapp');
