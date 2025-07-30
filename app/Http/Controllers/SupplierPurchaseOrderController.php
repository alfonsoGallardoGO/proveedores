<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SupplierPurchaseOrder;

class SupplierPurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = SupplierPurchaseOrder::latest()->get();
            $orders = $orders->map(function ($order) {
            $order->data = $order->data ? json_decode($order->data, true) : null;
            return $order;
        });

        return Inertia::render('Suppliers/PurchaseOrders/Index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/PurchaseOrders/Create');
    }

    public function show($id){
        $order = SupplierPurchaseOrder::findOrFail($id);
        $order->data = $order->data ? json_decode($order->data, true) : null;
        return response()->json($order);
    }
}
