<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderItem;

class SupplierPurchaseOrderController extends Controller
{
    public function index(){
        $orders = SupplierPurchaseOrder::with('items')
            ->latest()
            ->get();
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
        $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $id)->get();
        return response()->json($items);
    }
}
