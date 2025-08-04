<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderItem;
use App\Models\SupplierPurchaseOrdersItemsDelivery;
use App\Models\SupplierInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupplierPurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = SupplierPurchaseOrder::with('items')
        ->get();

        return Inertia::render('Suppliers/PurchaseOrders/Index', [
            'orders' => $orders
        ]);
    }


    public function create()
    {
        return Inertia::render('Suppliers/PurchaseOrders/Create');
    }

    public function show($id){
       $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $id)
        ->withSum('deliveries', 'amount')
        ->get();

        $invoices = SupplierInvoice::where('supplier_purchase_order_id', $id)->get();

        return response()->json([
            'items' => $items,
            'invoices' => $invoices,
        ]);

    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'cantidades' => 'required|array',
            'cantidades.*' => 'nullable|integer|min:0',
            'factura' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'xml' => 'nullable|file|mimes:xml|max:1024',
        ]);

        foreach ($data['cantidades'] as $itemId => $amount) {
            SupplierPurchaseOrdersItemsDelivery::create([
                'supplier_purchase_orders_item_id' => $itemId,
                'amount' => $amount,
            ]);
        }

        $supplierId = Auth::user()->supplier_id ?? 1; 
        $pdfPath = null;
        $xmlPath = null;

        if ($request->hasFile('factura')) {
            Storage::disk('public')->makeDirectory('invoices/pdf');
            $pdfPath = $request->file('factura')->store('invoices/pdf', 'public');
        }

        if ($request->hasFile('xml')) {
            Storage::disk('public')->makeDirectory('invoices/xml');
            $xmlPath = $request->file('xml')->store('invoices/xml', 'public');
        }

        SupplierInvoice::create([
            'supplier_id' => $supplierId,
            'supplier_purchase_order_id' => $request->supplier_purchase_order_id ?? 0,
            'pdf_route' => $pdfPath,
            'xml_route' => $xmlPath,
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Cantidades entregadas e invoices guardados correctamente.');
    }


}
