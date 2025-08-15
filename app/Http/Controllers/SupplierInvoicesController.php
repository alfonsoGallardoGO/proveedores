<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderItem;
use App\Models\SupplierPurchaseOrdersItemsDelivery;
use App\Models\SupplierInvoice;
use Illuminate\Support\Facades\Auth;

class SupplierInvoicesController extends Controller
{
    public function index()
    {
        $orders = SupplierPurchaseOrder::where('supplier_id', $supplierId = Auth::user()->supplier_id ?? 65424) -> with('items')
        ->get();

        return Inertia::render('Suppliers/Invoices/Index', [
            'orders' => $orders
        ]);
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

    // public function store(Request $request){
    //     $data = $request->validate([
    //         'cantidades' => 'required|array',
    //         'cantidades.*' => 'nullable|integer|min:0'
    //     ]);

    //     foreach ($data['cantidades'] as $itemId => $amount) {
    //         SupplierPurchaseOrdersItemsDelivery::create([
    //             'supplier_purchase_orders_item_id' => $itemId,
    //             'amount' => $amount,
    //         ]);
    //     }

    //     $supplierId = Auth::user()->supplier_id;
    //     $pdfPath = null;
    //     $xmlPath = null;

    //     if ($request->hasFile('pdf')) {
    //         $pdfPath = $request->file('pdf')->store('invoices/pdf', 'public');
    //     }

    //     if ($request->hasFile('xml')) {
    //         $xmlPath = $request->file('xml')->store('invoices/xml', 'public');
    //     }

    //     SupplierInvoice::create([
    //         'supplier_id' => 1,
    //         'supplier_purchase_order_id' =>$data['supplier_purchase_order_id'],
    //         'pdf_route' => $pdfPath,
    //         'xml_route' => $xmlPath,
    //     ]);

    //     return redirect()->route('purchase-orders.index')
    //         ->with('success', 'Cantidades entregadas registradas correctamente.');
    // }

}
