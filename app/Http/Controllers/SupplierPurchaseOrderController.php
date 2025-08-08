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
use Illuminate\Support\Facades\DB;

class SupplierPurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = SupplierPurchaseOrder::where('supplier_external_id', $supplierId = Auth::user()->supplier_id ?? 65424)
            ->get();
        return Inertia::render('Suppliers/PurchaseOrders/Index', [
            'orders' => $orders
        ]);
    }


    public function create()
    {
        return Inertia::render('Suppliers/PurchaseOrders/Create');
    }

    public function show($id)
    {
        $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $id)
            ->withSum('deliveries', 'amount')
            ->get();

        $invoices = SupplierInvoice::where('supplier_purchase_order_id', $id)->get();


        return Inertia::render('Suppliers/PurchaseOrders/Edit', [
            'items' => $items,
            'invoices' => $invoices,
        ]);
    }
    public function store(Request $request)
    {
        return "jotolon";
        // $data = $request;

        // $gastos = [];
        // $articulos = [];
        // foreach ($data['cantidades'] as $itemId => $amount) {
        //     SupplierPurchaseOrdersItemsDelivery::create([
        //         'supplier_purchase_orders_item_id' => $itemId,
        //         'amount' => $amount ?? 0,
        //     ]);

        // }

        // $supplierId = Auth::user()->supplier_id ?? 1;
        // $pdfPath = null;
        // $xmlPath = null;

        // if ($request->hasFile('factura')) {
        //     Storage::disk('public')->makeDirectory('invoices/pdf');
        //     $pdfPath = $request->file('factura')->store('invoices/pdf', 'public');

        //     $pdfContent = Storage::disk('public')->get($pdfPath);
        //     $pdfBase64  = base64_encode($pdfContent);

        // }

        // if ($request->hasFile('xml')) {
        //     Storage::disk('public')->makeDirectory('invoices/xml');
        //     $xmlPath = $request->file('xml')->store('invoices/xml', 'public');

        //     $xmlContent = Storage::disk('public')->get($xmlPath);
        //     $xmlBase64  = base64_encode($xmlContent);

        // }

        // SupplierInvoice::create([
        //     'supplier_id' => $supplierId,
        //     'supplier_purchase_order_id' => $request->supplier_purchase_order_id ?? 0,
        //     'pdf_route' => $pdfPath,
        //     'xml_route' => $xmlPath,
        // ]);

        // $order = SupplierPurchaseOrder::where('purchase_order_id', $request->supplier_purchase_order_id)
        //     ->get();



        // $path = storage_path('app/public/invoices/xml/vM7xw0qvKIXTyT8BV7ixNQAt0WQceSdC1fJUmbAZ.xml');
        // $xmlContent = file_get_contents($path);
        // $xml = simplexml_load_string($xmlContent);

        // $ns = $xml->getNamespaces(true);
        // $xml->registerXPathNamespace('cfdi', $ns['cfdi']);
        // $xml->registerXPathNamespace('tfd',  $ns['tfd']);
        // $xml->registerXPathNamespace('pago20', $ns['pago20']);

        

        // $emisorNode   = $xml->xpath('//cfdi:Emisor')[0] ?? null;
        // $receptorNode = $xml->xpath('//cfdi:Receptor')[0] ?? null;

        // $rfcEmisor    = $emisorNode ? (string)$emisorNode['Rfc'] : '';
        // $rfcReceptor  = $receptorNode ? (string)$receptorNode['Rfc'] : '';
        
        // $pagoNode = $xml->xpath('//pago20:Pago')[0] ?? null;
        // $monedaP  = $pagoNode ? (string)$pagoNode['MonedaP'] : 'MXN';
        // $tipoCambioP = $pagoNode ? (string)$pagoNode['TipoCambioP'] : '1';
        // $montoPago   = $pagoNode ? (string)$pagoNode['Monto'] : '0';

        // $conceptoNode = $xml->xpath('//cfdi:Concepto')[0] ?? null;
        // $claveProdServPago = $conceptoNode ? (string)$conceptoNode['ClaveProdServ'] : '0';


        // $trasladosP = $xml->xpath('//pago20:TrasladoP') ?? [];

        // $termino      = "4";
        // $departamento = "106";
        // $clase        = "490";
        // $operacion    = "3";
        // $ubicacion    = "533";
        // $categoria    = "125";

        // $gastos = [];
        // foreach ($trasladosP as $item_traslado) {
        //     $baseP      = (string)$item_traslado['BaseP'];   
        //     $impuestoP  = (string)$item_traslado['ImpuestoP'];
        //     $tipoFactor = (string)$item_traslado['TipoFactorP'];
        //     $tasaOCuota = (string)$item_traslado['TasaOCuotaP'];
        //     $importeP   = (string)$item_traslado['ImporteP'];

        //     $gastos[] = [
        //         "categoria"     => $categoria,
        //         "costo"         => (string) (float) $baseP,
        //         "ubicacion"     => $ubicacion,
        //         "departamento"  => $departamento,
        //         "clase"         => $clase,
        //         "concepto"      => "Pago",
        //         "claveprodser"  => $claveProdServPago,
        //         "Impuestos"     => [
        //             "Traslados" => [
        //                 "Traslado" => [[
        //                     "Base"       => (string) (float) $baseP,
        //                     "Impuesto"   => $impuestoP,
        //                     "TipoFactor" => $tipoFactor,
        //                     "TasaOCuota" => $tasaOCuota,
        //                     "Importe"    => (string) (float) $importeP,
        //                 ]]
        //             ]
        //         ]
        //     ];
        // }


        // $jsonPayload = [
        //     "rfc"            => 0 ,
        //     "nfactura"       => "27249",
        //     "regimenfiscal"  => "626",
        //     "moneda"         => "MXN",
        //     "termino"        => "4",
        //     "departamento"   => "106",
        //     "clase"          => "490",
        //     "operacion"      => "3",
        //     "tipocambio"     => 0,
        //     "fecha"          => "04/08/2025",
        //     "ubicacion"      => "533",
        //     "idnetsuite"     => "",
        //     "uuid"           => "AB05BA85-C615-456C-8580-600D3C0EDDA8",
        //     "gastos"         =>$gastos,
        //     "articulos"      => 0,
        //     "nota"           => "",
        //     "generico"       => "",
        //     "xml" => 0,
        //     "pdf" => 0
        // ];

        // return 1;

        // return redirect()->route('purchase-orders.index')
        //     ->with('success', 'Cantidades entregadas e invoices guardados correctamente.');
    }

    public function storePurchaseOrder(Request $request)
    {
        $data = $request->all();
        $supplier_purchase_order_id = $data['id'] ?? null;

        if (empty($supplier_purchase_order_id)) {
            return response()->json([
                'error' => 'El campo id es requerido.'
            ], 400);
        }

        if (empty($data['estado'])) {
            return response()->json([
                'error' => 'El campo estado es requerido.'
            ], 400);
        }

        if (empty($data['tranid']) || strpos($data['tranid'], 'OC') !== 0) {
            return response()->json([
                'error' => 'El campo tranid no es valido.'
            ], 400);
        }

        $orderId = 0;
        $supplierPurchaseOrder = SupplierPurchaseOrder::where('purchase_order_id', $supplier_purchase_order_id);
        if ($supplierPurchaseOrder->exists()) {
            $supplierPurchaseOrder->update([
                'supplier_external_id' => $data['proveedor']['id'] ?? null,
                'rfc' => $data['proveedor']['rfc'] ?? null,
                'status' => $data['estado'] ?? null,
                'date' => isset($data['fecha']) ? date('Y-m-d', strtotime($data['fecha'])) : null,
                'purchase_order_id' => $data['id'] ?? null,
                'purchase_order' => $data['tranid'] ?? null,
                'total' => $data['total'] ?? null,
                'subtotal' => $data['subtotal'] ?? null,
                'impuesto' => $data['impuesto'] ?? null,
            ]);
            $orderId = $supplierPurchaseOrder->first()->id;
        } else {
            SupplierPurchaseOrder::create([
                'supplier_external_id' => $data['proveedor']['id'] ?? null,
                'rfc' => $data['proveedor']['rfc'] ?? null,
                'status' => $data['estado'] ?? null,
                'date' => isset($data['fecha']) ? date('Y-m-d', strtotime($data['fecha'])) : null,
                'purchase_order_id' => $data['id'] ?? null,
                'purchase_order' => $data['tranid'] ?? null,
                'total' => $data['total'] ?? null,
                'subtotal' => $data['subtotal'] ?? null,
                'impuesto' => $data['impuesto'] ?? null,

            ]);
            $orderId = SupplierPurchaseOrder::latest()->first()->id;
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
                'supplier_purchase_order_id' => $orderId,
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
                'supplier_purchase_order_id' => $orderId,
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
}
