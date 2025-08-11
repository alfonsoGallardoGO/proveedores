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
use App\Services\NetSuiteRestService;
use Illuminate\Support\Facades\Log;

class SupplierPurchaseOrderController extends Controller
{
    private NetSuiteRestService $netSuiteRestService;
    public function __construct(NetSuiteRestService $netSuiteRestService)
    {
        $this->netSuiteRestService = $netSuiteRestService;
    }

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
        // dd($id);
        $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $id)
            ->withSum('deliveries', 'amount')
            ->get();
        // dd($items);

        $invoices = SupplierInvoice::where('supplier_purchase_order_id', $id)->get();

        // dd($invoices);

        return Inertia::render('Suppliers/PurchaseOrders/Edit', [
            'items' => $items,
            'invoices' => $invoices,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request;

        $gastos = [];
        $articulos = [];
        foreach ($data['cantidades'] as $itemId => $amount) {
            SupplierPurchaseOrdersItemsDelivery::create([
                'supplier_purchase_orders_item_id' => $itemId,
                'amount' => $amount ?? 0,
            ]);

        }

        $supplierId = Auth::user()->supplier_id ?? 1;
        $pdfPath = null;
        $xmlPath = null;
        

        if ($request->hasFile('factura')) {
            Storage::disk('public')->makeDirectory('invoices/pdf');
            $pdfPath = $request->file('factura')->store('invoices/pdf', 'public');

            $pdfContent = Storage::disk('public')->get($pdfPath);
            $pdfBase64  = base64_encode($pdfContent);

        }

        if ($request->hasFile('xml')) {
            Storage::disk('public')->makeDirectory('invoices/xml');
            $xmlPath = $request->file('xml')->store('invoices/xml', 'public');

            $xmlContent = Storage::disk('public')->get($xmlPath);
            $xmlBase64  = base64_encode($xmlContent);

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


    public function xml(Request $request)
    {
        
        $path = storage_path('app/public/invoices/xml/jdsL7BXETZ8dMpw9XgWWGWQPLDhRn1pqgTuiC7xK.xml');

        if (!file_exists($path)) {
            abort(404, 'XML no encontrado');
        }

        $xp = function($xml, $query) { return $xml->xpath($query) ?: []; };
        $first = function($arr) { return is_array($arr) && count($arr) ? $arr[0] : null; };
        $attr = function($node, $name) { return $node ? (string)($node[$name] ?? '') : ''; };

        libxml_use_internal_errors(true);
        $xmlContent = file_get_contents($path);
        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            abort(422, 'XML inválido');
        }

        $ns = $xml->getNamespaces(true);
        if (isset($ns['cfdi']))   $xml->registerXPathNamespace('cfdi',   $ns['cfdi']);
        if (isset($ns['tfd']))    $xml->registerXPathNamespace('tfd',    $ns['tfd']);
        if (isset($ns['pago20'])) $xml->registerXPathNamespace('pago20', $ns['pago20']);

        $emisorNode   = $first($xp($xml, '//cfdi:Emisor'));
        $receptorNode = $first($xp($xml, '//cfdi:Receptor'));
        $tfdNode      = $first($xp($xml, '//tfd:TimbreFiscalDigital'));

        $serie     = (string)($xml['Serie'] ?? '');
        $folio     = (string)($xml['Folio'] ?? '');
        $fechaCfd  = (string)($xml['Fecha'] ?? '');
        $fechaJson = $fechaCfd ? \Carbon\Carbon::parse($fechaCfd)->format('d/m/Y') : '';

        $rfcEmisor      = $attr($emisorNode, 'Rfc');
        $regimenFiscal  = $attr($emisorNode, 'RegimenFiscal');
        $rfcReceptor    = $attr($receptorNode, 'Rfc');
        $uuid           = $attr($tfdNode, 'UUID');

        $isPago = isset($ns['pago20']) && count($xp($xml, '//pago20:Pago')) > 0;

        if ($isPago) {
            $pagoNode   = $first($xp($xml, '//pago20:Pago'));
            $moneda     = $attr($pagoNode, 'MonedaP') ?: 'MXN';
            $tipoCambio = $attr($pagoNode, 'TipoCambioP') ?: '1';
        } else {
            $moneda     = (string)($xml['Moneda'] ?? 'MXN');
            $tipoCambio = (string)($xml['TipoCambio'] ?? '1');
        }

        $conceptoNode   = $first($xp($xml, '//cfdi:Concepto'));
        $claveProdServ  = $attr($conceptoNode, 'ClaveProdServ') ?: '84111506';
        $descripcion    = $attr($conceptoNode, 'Descripcion') ?: 'Concepto';

        $gastos = [];

        if ($isPago) {
            $trasladosP = $xp($xml, '//pago20:TrasladoP');
            if (!empty($trasladosP)) {
                $docRel = $first($xp($xml, '//pago20:DoctoRelacionado'));
                $conceptoTxt = trim('Pago relacionado ' . $attr($docRel, 'Serie') . ' ' . $attr($docRel, 'Folio'));

                foreach ($trasladosP as $t) {
                    $base = (string)$t['BaseP'];
                    $gastos[] = [
                        "categoria"     => "125",
                        "costo"         => (string)(float)$base,
                        "ubicacion"     => "533",
                        "departamento"  => "106",
                        "clase"         => "490",
                        "concepto"      => $conceptoTxt ?: 'Pago',
                        "claveprodser"  => $claveProdServ,
                        "Impuestos"     => [
                            "Traslados" => [
                                "Traslado" => [[
                                    "Base"       => (string)(float)$base,
                                    "Impuesto"   => (string)$t['ImpuestoP'],
                                    "TipoFactor" => (string)$t['TipoFactorP'],
                                    "TasaOCuota" => (string)$t['TasaOCuotaP'],
                                    "Importe"    => (string)(float)$t['ImporteP'],
                                ]]
                            ]
                        ]
                    ];
                }
            }

            if (empty($gastos)) {
                $trasladosDR = $xp($xml, '//pago20:TrasladoDR');
                foreach ($trasladosDR as $t) {
                    $base = (string)$t['BaseDR'];
                    $gastos[] = [
                        "categoria"     => "125",
                        "costo"         => (string)(float)$base,
                        "ubicacion"     => "533",
                        "departamento"  => "106",
                        "clase"         => "490",
                        "concepto"      => $descripcion ?: 'Pago',
                        "claveprodser"  => $claveProdServ,
                        "Impuestos"     => [
                            "Traslados" => [
                                "Traslado" => [[
                                    "Base"       => (string)(float)$base,
                                    "Impuesto"   => (string)$t['ImpuestoDR'],
                                    "TipoFactor" => (string)$t['TipoFactorDR'],
                                    "TasaOCuota" => (string)$t['TasaOCuotaDR'],
                                    "Importe"    => (string)(float)$t['ImporteDR'],
                                ]]
                            ]
                        ]
                    ];
                }
            }
        }

        if (empty($gastos)) {
            $trasGlobal = $xp($xml, '//cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
            foreach ($trasGlobal as $t) {
                $base = (string)($t['Base'] ?? $t['Importe'] ?? '0');
                $gastos[] = [
                    "categoria"     => "125",
                    "costo"         => (string)(float)$base,
                    "ubicacion"     => "533",
                    "departamento"  => "106",
                    "clase"         => "490",
                    "concepto"      => $descripcion,
                    "claveprodser"  => $claveProdServ,
                    "Impuestos"     => [
                        "Traslados" => [
                            "Traslado" => [[
                                "Base"       => (string)(float)$base,
                                "Impuesto"   => (string)$t['Impuesto'],
                                "TipoFactor" => (string)$t['TipoFactor'],
                                "TasaOCuota" => (string)$t['TasaOCuota'],
                                "Importe"    => (string)(float)$t['Importe'],
                            ]]
                        ]
                    ]
                ];
            }
        }

        if (empty($gastos)) {
            $conceptos = $xp($xml, '//cfdi:Concepto');
            foreach ($conceptos as $c) {
                $trasC = $xp($c, './cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
                foreach ($trasC as $t) {
                    $base = (string)($t['Base'] ?? $t['Importe'] ?? '0');
                    $gastos[] = [
                        "categoria"     => "125",
                        "costo"         => (string)(float)$base,
                        "ubicacion"     => "533",
                        "departamento"  => "106",
                        "clase"         => "490",
                        "concepto"      => (string)($c['Descripcion'] ?? $descripcion),
                        "claveprodser"  => (string)($c['ClaveProdServ'] ?? $claveProdServ),
                        "Impuestos"     => [
                            "Traslados" => [
                                "Traslado" => [[
                                    "Base"       => (string)(float)$base,
                                    "Impuesto"   => (string)$t['Impuesto'],
                                    "TipoFactor" => (string)$t['TipoFactor'],
                                    "TasaOCuota" => (string)$t['TasaOCuota'],
                                    "Importe"    => (string)(float)$t['Importe'],
                                ]]
                            ]
                        ]
                    ];
                }
            }
        }

        // $xmlBase64 = base64_encode($xmlContent);
        // $pdfBase64 = "";

        $pdfPath = storage_path('app/public/invoices/pdf/3Lt5H7cPWRJCi7sQXCPaefhwgu0UnyLdzq6RBDDu.pdf');
        $xmlPath = storage_path('app/public/invoices/xml/jdsL7BXETZ8dMpw9XgWWGWQPLDhRn1pqgTuiC7xK.xml');

        $pdfBase64 = base64_encode(file_get_contents($pdfPath));
        $xmlBase64 = base64_encode(file_get_contents($xmlPath));

        $jsonPayload = [
            "rfc"           => $rfcEmisor ?: $rfcReceptor,
            "nfactura"      => $folio, 
            "regimenfiscal" => $regimenFiscal,
            "moneda"        => $moneda,
            "termino"       => "4", 
            "departamento"  => "106",
            "clase"         => "490",
            "operacion"     => "3",
            "tipocambio"    => (float)$tipoCambio,
            "fecha"         => $fechaJson,
            "ubicacion"     => "533",
            "idnetsuite"    => "",
            "uuid"          => $uuid,
            "gastos"        => $gastos,
            "articulos"     => [],
            "nota"          => "",
            "generico"      => "",
            "xml"           => $xmlBase64,
            "pdf"           => $pdfBase64,
        ];
        

        $data = [
            "idproveedor" => "65424",
            "iddoc" => "4414137",
            "tipo_doc" => "PurchOrd",
            "rfc" => "SET0912148T6",
            "nfactura" => "74186",
            "regimenfiscal" => "601",
            "moneda" => "MXN",
            "termino" => "4",
            "departamento" => "105",
            "clase" => "447",
            "operacion" => "3",
            "tipocambio" => 0,
            "fecha" => "11/08/2025",
            "ubicacion" => "128",
            "idnetsuite" => "",
            "uuid" => "729D0E96-CEDB-4C50-AAAD-13CB4A797065",
            "gastos" => [
                [
                    "categoria" => "112",
                    "costo" => "68.97",
                    "ubicacion" => "128",
                    "departamento" => "105",
                    "clase" => "447",
                    "concepto" => "Telefonia Telefonía Neg Ilim Plus Mensualidad Princ - Del 01/08/2025 al 31/08/2025",
                    "claveprodser" => "81161700",
                    "Impuestos" => [
                        "Traslados" => [
                            "Traslado" => [
                                [
                                    "Base" => "68.970000",
                                    "Impuesto" => "002",
                                    "TipoFactor" => "Tasa",
                                    "TasaOCuota" => "0.160000",
                                    "Importe" => "11.03"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "articulos" => [],
            "nota" => "TELEFONIA NEG LLIUM PLUS MES DE AGOSTO",
            "generico" => "8345",
            "xml" =>$xmlBase64,
            "pdf" =>$pdfBase64
        ];

        

        // return $data;

        $restletPath = "/restlet.nl?script=5141&deploy=1";
        try {
            $response = $this->netSuiteRestService->request($restletPath, 'POST', $data);
            return response()->json(['ok' => true, 'response' => $response]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
