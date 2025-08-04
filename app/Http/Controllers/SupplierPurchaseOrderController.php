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

<<<<<<< HEAD
=======
    public function storePurchaseOrder(Request $request)
    {
        $data = $request->all();
        $supplier_purchase_order_id = $data['id'] ?? null;

        if (empty($supplier_purchase_order_id)) {
            return response()->json([
                'error' => 'El campo id es requerido.'
            ], 400);
        }

        $incomingItems = [];

        // Procesar lineasArticulos
        foreach (collect($data['lineasArticulos'] ?? []) as $item) {
            $standardizedItem = [
                'article_order_id' => $item['articuloId'],
                'description'      => $item['descripcion'],
                'quantity'         => $item['cantidad'],
                'amount'           => $item['importe'],
                'rate_tax'         => $item['tasaImpuesto'],
                'class'            => $item['clase'],
                'department'       => $item['departamento'],
                'location'         => $item['ubicacion'],
                'account'          => $item['cuenta'],
                'categoria'        => $item['categoria'],
                'memo'             => $item['memo'] ?? null,
                'type'             => 'ARTICULO',
                'supplier_purchase_order_id' => $supplier_purchase_order_id,
            ];
            $uniqueKey = $standardizedItem['article_order_id'] . '_' . $standardizedItem['type'];
            $incomingItems[$uniqueKey] = $standardizedItem;
        }

        // Procesar lineasGastos
        foreach (collect($data['lineasGastos'] ?? []) as $item) {
            $standardizedItem = [
                'article_order_id' => $item['articuloId'],
                'description'      => $item['memo'],
                'quantity'         => $item['cantidad'],
                'amount'           => $item['importe'],
                'rate_tax'         => $item['tasaImpuesto'],
                'class'            => $item['clase'],
                'department'       => $item['departamento'],
                'location'         => $item['ubicacion'],
                'account'          => $item['cuenta'],
                'categoria'        => $item['categoria'],
                'memo'             => $item['memo'] ?? null,
                'type'             => 'GASTO', 
                'supplier_purchase_order_id' => $supplier_purchase_order_id,
            ];

            $uniqueKey = $standardizedItem['article_order_id'] . '_' . $standardizedItem['type'];
            $incomingItems[$uniqueKey] = $standardizedItem;
        }

        //$existingItems = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $supplier_purchase_order_id)
                                                // ->get()
                                                // ->keyBy(function($item) {
                                                //     // Creamos la misma clave única para comparar
                                                //     return $item->article_order_id . '_' . $item->type;
                                                // });

        
        $itemsToKeepInDb = []; 

        DB::beginTransaction(); 
        try {
            foreach ($incomingItems as $uniqueKey => $incomingItemData) {
                $existingItem = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $supplier_purchase_order_id)
                                                        ->where('article_order_id', $incomingItemData['article_order_id'])
                                                        ->where('type', $incomingItemData['type'])
                                                        ->withTrashed() 
                                                        ->first();

                if ($existingItem) {
                    if ($existingItem->trashed()) {
                        $existingItem->restore();
                    }

                    $existingItem->update($incomingItemData);
                    $itemsToKeepInDb[] = $existingItem->id;

                } else {
                    
                    $newItem = SupplierPurchaseOrderItem::create($incomingItemData);
                    $itemsToKeepInDb[] = $newItem->id;
                }
            }

           
            $idsToDelete = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $supplier_purchase_order_id)
                                                    ->whereNotIn('id', $itemsToKeepInDb)
                                                    ->pluck('id')
                                                    ->all();

            if (!empty($idsToDelete)) {
                SupplierPurchaseOrderItem::whereIn('id', $idsToDelete)->delete(); 
            }

            $supplierPurchaseOrder = SupplierPurchaseOrder::where('purchase_order_id', $supplier_purchase_order_id);
            if($supplierPurchaseOrder->exists()){
                $supplierPurchaseOrder->update([
                    'supplier_external_id' => $data['proveedor']['id'] ?? null,
                    'rfc' => $data['proveedor']['rfc'] ?? null,
                    'status' => $data['estado'] ?? null,
                    'date' => isset($data['fecha']) ? date('Y-m-d', strtotime($data['fecha'])) : null,
                    'purchase_order_id' => $data['id'] ?? null,
                    'purchase_order' => $data['tranid'] ?? null,
                ]);
            } else {
                SupplierPurchaseOrder::create([
                    'supplier_external_id' => $data['proveedor']['id'] ?? null,
                    'rfc' => $data['proveedor']['rfc'] ?? null,
                    'status' => $data['estado'] ?? null,
                    'date' => isset($data['fecha']) ? date('Y-m-d', strtotime($data['fecha'])) : null,
                    'purchase_order_id' => $data['id'] ?? null,
                    'purchase_order' => $data['tranid'] ?? null,
                ]);
            }

            DB::commit(); 
            return response()->json([
                'message' => 'Orden de compra y sus ítems sincronizados correctamente.',
                'supplier_purchase_order_id' => $supplier_purchase_order_id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al procesar la orden de compra.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
>>>>>>> parent of a78d4cb (feat:validaciones a las ordenes de compra)

}
