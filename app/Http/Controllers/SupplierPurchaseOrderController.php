<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SupplierPurchaseOrder;
use App\Models\SupplierPurchaseOrderItem;
use App\Models\SupplierPurchaseOrdersItemsDelivery;
use App\Models\SupplierInvoice;
use App\Models\Supplier;
use App\Models\NetsuiteAccountingAccounts;
use App\Models\NetsuiteClass;
use App\Models\NetsuiteDepartments;
use App\Models\NetsuiteExpenseCategories;
use App\Models\NetsuiteLocations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\NetSuiteRestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Services\CfdiParser;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


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
            $file = $request->file('factura');
            abort_unless($file->isValid(), 422, 'Archivo inválido');


            $folderPath = public_path('suppliers/invoices/pdf');
            File::ensureDirectoryExists($folderPath, 0755, true);


            $ext = $file->getClientOriginalExtension() ?: 'pdf';
            $filename = 'receipt_' . (string) Str::uuid() . '.' . $ext;


            $file->move($folderPath, $filename);


            $pdfAbsolute = $folderPath . DIRECTORY_SEPARATOR . $filename;
            $pdfPath      = asset('suppliers/invoices/pdf/' . $filename);

            $pdfBase64 = base64_encode(file_get_contents($pdfAbsolute));
        }

        if ($request->hasFile('xml')) {
            $file = $request->file('xml');
            abort_unless($file->isValid(), 422, 'Archivo inválido');


            $folderPath = public_path('suppliers/invoices/xml');
            File::ensureDirectoryExists($folderPath, 0755, true);


            $ext = $file->getClientOriginalExtension() ?: 'xml';
            $filename = 'receipt_' . (string) Str::uuid() . '.' . $ext;


            $file->move($folderPath, $filename);


            $pdfAbsolute = $folderPath . DIRECTORY_SEPARATOR . $filename;
            $xmlPath      = asset('suppliers/invoices/xml/' . $filename);

            $xmlBase64 = base64_encode(file_get_contents($pdfAbsolute));
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
        return $data;
        $supplier_purchase_order_id = $data['id'] ?? null;
        
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = $jsonData . "\n"; 
        file_put_contents(storage_path('debug_input.json'), $jsonData);

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
                'total' => $data['total'],
                'subtotal' => $data['subtotal'],
                'impuesto' => $data['impuesto'],
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
                'total' => $data['total'],
                'subtotal' => $data['subtotal'],
                'impuesto' => $data['impuesto'],

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

        $purchase_order_id = 873;
        $pdfPath = public_path('suppliers/invoices/pdf/prueba_3.pdf');
        $xmlPath = public_path('suppliers/invoices/xml/prueba_3.xml');


        if (!File::exists($xmlPath)) {
            return response()->json(['error' => 'XML no encontrado'], 404);
        }


        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();

        $loaded = $dom->load($xmlPath, LIBXML_NONET | LIBXML_NOBLANKS);
        if (!$loaded) {
            $errs = array_map(fn($e) => trim($e->message), libxml_get_errors());
            libxml_clear_errors();
            return response()->json(['ok' => false, 'error' => 'No se pudo cargar el XML', 'detalles' => $errs], 422);
        }
        $xp = new \DOMXPath($dom);

        $root = $dom->documentElement;
        $nsUri = $root?->namespaceURI ?? '';
        if ($nsUri) {
            $xp->registerNamespace('cfdi', $nsUri);
        } else {
            $xp->registerNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
        }
        $xp->registerNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');

        $compPath = '//cfdi:Comprobante';

        $data = [
            'version'            => $xp->evaluate("string($compPath/@Version)"),
            'serie'              => $xp->evaluate("string($compPath/@Serie)"),
            'folio'              => $xp->evaluate("string($compPath/@Folio)"),
            'fecha'              => $xp->evaluate("string($compPath/@Fecha)"),
            'subtotal'           => $xp->evaluate("string($compPath/@SubTotal)"),
            'descuento'          => $xp->evaluate("string($compPath/@Descuento)"),
            'moneda'             => $xp->evaluate("string($compPath/@Moneda)"),
            'tipo_cambio'        => $xp->evaluate("string($compPath/@TipoCambio)"),
            'total'              => $xp->evaluate("string($compPath/@Total)"),
            'forma_pago'         => $xp->evaluate("string($compPath/@FormaPago)"),
            'metodo_pago'        => $xp->evaluate("string($compPath/@MetodoPago)"),
            'tipo_de_comprobante' => $xp->evaluate("string($compPath/@TipoDeComprobante)"),
            'exportacion'        => $xp->evaluate("string($compPath/@Exportacion)"),
            'emisor' => [
                'rfc'     => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Emisor/@Rfc)'),
                'nombre'  => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Emisor/@Nombre)'),
                'regimen' => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Emisor/@RegimenFiscal)'),
                'clave_prod_serv' => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[1]/@ClaveProdServ)')
            ],
            'receptor' => [
                'rfc'     => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Receptor/@Rfc)'),
                'nombre'  => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Receptor/@Nombre)'),
                'uso_cfdi' => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Receptor/@UsoCFDI)'),
            ],
            'timbre' => [
                'uuid'           => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/tfd:TimbreFiscalDigital/@UUID)'),
                'fechaTimbrado'  => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/tfd:TimbreFiscalDigital/@FechaTimbrado)'),
                'noCertSAT'      => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/tfd:TimbreFiscalDigital/@NoCertificadoSAT)'),
            ],
        ];

        $xp->registerNamespace('pago20', 'http://www.sat.gob.mx/Pagos20');


        $pagos20 = [
            'totales' => [
                'MontoTotalPagos'             => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales/@MontoTotalPagos)'),
                'TotalRetencionesIVA'         => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalRetencionesIVA)'),
                'TotalRetencionesISR'         => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalRetencionesISR)'),
                'TotalTrasladosBaseIVA16'     => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalTrasladosBaseIVA16)'),
                'TotalTrasladosImpuestoIVA16' => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalTrasladosImpuestoIVA16)'),
            ],
            'pagos' => [],
        ];

        $xp->registerNamespace('pago20', 'http://www.sat.gob.mx/Pagos20');

        $tipo = $xp->evaluate('string(//cfdi:Comprobante/@TipoDeComprobante)');

        $pagos20 = [
            'totales' => [
                'MontoTotalPagos'             => null,
                'TotalRetencionesIVA'         => null,
                'TotalRetencionesISR'         => null,
                'TotalTrasladosBaseIVA16'     => null,
                'TotalTrasladosImpuestoIVA16' => null,
            ],
            'pagos' => [],
        ];

        if ($tipo === 'P') {

            $pagos20['totales'] = [
                'MontoTotalPagos'             => $xp->evaluate('string(//cfdi:Complemento/pago20:Pagos/pago20:Totales/@MontoTotalPagos)'),
                'TotalRetencionesIVA'         => $xp->evaluate('string(//cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalRetencionesIVA)'),
                'TotalRetencionesISR'         => $xp->evaluate('string(//cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalRetencionesISR)'),
                'TotalTrasladosBaseIVA16'     => $xp->evaluate('string(//cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalTrasladosBaseIVA16)'),
                'TotalTrasladosImpuestoIVA16' => $xp->evaluate('string(//cfdi:Complemento/pago20:Pagos/pago20:Totales/@TotalTrasladosImpuestoIVA16)'),
            ];

            $pagoNodes = $xp->query('//cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Pago');

            if (!$pagoNodes || $pagoNodes->length === 0) {
                $pagoNodes = $dom->getElementsByTagNameNS('http://www.sat.gob.mx/Pagos20', 'Pago');
            }

            foreach ($pagoNodes as $pago) {
                /** @var \DOMElement $pago */
                $pagoData = [
                    'FechaPago'    => $pago->getAttribute('FechaPago'),
                    'FormaDePagoP' => $pago->getAttribute('FormaDePagoP'),
                    'MonedaP'      => $pago->getAttribute('MonedaP'),
                    'TipoCambioP'  => $pago->getAttribute('TipoCambioP'),
                    'Monto'        => $pago->getAttribute('Monto'),
                    'NumOperacion' => $pago->getAttribute('NumOperacion'),
                    'rfcEmisorCtaOrd' => $pago->getAttribute('RfcEmisorCtaOrd'),
                    'ctaOrdenante'    => $pago->getAttribute('CtaOrdenante'),
                    'rfcEmisorCtaBen' => $pago->getAttribute('RfcEmisorCtaBen'),
                    'ctaBeneficiario' => $pago->getAttribute('CtaBeneficiario'),
                    'doctos_relacionados' => [],
                    'impuestosP' => [
                        'retencionesP' => [],
                        'trasladosP'   => [],
                    ],
                ];

                $doctoNodes = $xp->query('pago20:DoctoRelacionado', $pago);
                foreach ($doctoNodes as $doc) {
                    /** @var \DOMElement $doc */
                    $docto = [
                        'IdDocumento'      => $doc->getAttribute('IdDocumento'),
                        'Serie'            => $doc->getAttribute('Serie'),
                        'Folio'            => $doc->getAttribute('Folio'),
                        'MonedaDR'         => $doc->getAttribute('MonedaDR'),
                        'EquivalenciaDR'   => $doc->getAttribute('EquivalenciaDR'),
                        'NumParcialidad'   => $doc->getAttribute('NumParcialidad'),
                        'ImpPagado'        => $doc->getAttribute('ImpPagado'),
                        'ImpSaldoAnt'      => $doc->getAttribute('ImpSaldoAnt'),
                        'ImpSaldoInsoluto' => $doc->getAttribute('ImpSaldoInsoluto'),
                        'ObjetoImpDR'      => $doc->getAttribute('ObjetoImpDR'),
                        'impuestosDR' => [
                            'trasladosDR'   => [],
                            'retencionesDR' => [],
                        ],
                    ];

                    $trasDRNodes = $xp->query('pago20:ImpuestosDR/pago20:TrasladosDR/pago20:TrasladoDR', $doc);
                    foreach ($trasDRNodes as $t) {
                        /** @var \DOMElement $t */
                        $docto['impuestosDR']['trasladosDR'][] = [
                            'BaseDR'       => $t->getAttribute('BaseDR'),
                            'ImpuestoDR'   => $t->getAttribute('ImpuestoDR'),
                            'TipoFactorDR' => $t->getAttribute('TipoFactorDR'),
                            'TasaOCuotaDR' => $t->getAttribute('TasaOCuotaDR'),
                            'ImporteDR'    => $t->getAttribute('ImporteDR'),
                        ];
                    }
                    $retDRNodes = $xp->query('pago20:ImpuestosDR/pago20:RetencionesDR/pago20:RetencionDR', $doc);
                    foreach ($retDRNodes as $r) {
                        /** @var \DOMElement $r */
                        $docto['impuestosDR']['retencionesDR'][] = [
                            'BaseDR'       => $r->getAttribute('BaseDR'),
                            'ImpuestoDR'   => $r->getAttribute('ImpuestoDR'),
                            'TipoFactorDR' => $r->getAttribute('TipoFactorDR'),
                            'TasaOCuotaDR' => $r->getAttribute('TasaOCuotaDR'),
                            'ImporteDR'    => $r->getAttribute('ImporteDR'),
                        ];
                    }

                    $pagoData['doctos_relacionados'][] = $docto;
                }

                $retPNodes = $xp->query('pago20:ImpuestosP/pago20:RetencionesP/pago20:RetencionP', $pago);
                foreach ($retPNodes as $rP) {
                    /** @var \DOMElement $rP */
                    $pagoData['impuestosP']['retencionesP'][] = [
                        'ImpuestoP' => $rP->getAttribute('ImpuestoP'),
                        'ImporteP'  => $rP->getAttribute('ImporteP'),
                    ];
                }
                $trasPNodes = $xp->query('pago20:ImpuestosP/pago20:TrasladosP/pago20:TrasladoP', $pago);
                foreach ($trasPNodes as $tP) {
                    /** @var \DOMElement $tP */
                    $pagoData['impuestosP']['trasladosP'][] = [
                        'BaseP'       => $tP->getAttribute('BaseP'),
                        'ImpuestoP'   => $tP->getAttribute('ImpuestoP'),
                        'TipoFactorP' => $tP->getAttribute('TipoFactorP'),
                        'TasaOCuotaP' => $tP->getAttribute('TasaOCuotaP'),
                        'ImporteP'    => $tP->getAttribute('ImporteP'),
                    ];
                }

                $pagos20['pagos'][] = $pagoData;
            }
        } else {

            $conceptos = [];
            $conceptNodes = $xp->query('//cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto');
            foreach ($conceptNodes as $n) {
                /** @var \DOMElement $n */
                $c = [
                    'ClaveProdServ'  => $n->getAttribute('ClaveProdServ'),
                    'NoIdentificacion'=> $n->getAttribute('NoIdentificacion'),
                    'Cantidad'       => $n->getAttribute('Cantidad'),
                    'ClaveUnidad'    => $n->getAttribute('ClaveUnidad'),
                    'Unidad'         => $n->getAttribute('Unidad'),
                    'Descripcion'    => $n->getAttribute('Descripcion'),
                    'ValorUnitario'  => $n->getAttribute('ValorUnitario'),
                    'Importe'        => $n->getAttribute('Importe'),
                    'Descuento'      => $n->getAttribute('Descuento'),
                    'Impuestos'      => [
                        'Traslados'   => [],
                        'Retenciones' => [],
                    ],
                ];

                $tNodes = $xp->query('cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado', $n);
                foreach ($tNodes as $t) {
                    /** @var \DOMElement $t */
                    $c['Impuestos']['Traslados'][] = [
                        'Base'       => $t->getAttribute('Base'),
                        'Impuesto'   => $t->getAttribute('Impuesto'),
                        'TipoFactor' => $t->getAttribute('TipoFactor'),
                        'TasaOCuota' => $t->getAttribute('TasaOCuota'),
                        'Importe'    => $t->getAttribute('Importe'),
                    ];
                }
                $rNodes = $xp->query('cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion', $n);
                foreach ($rNodes as $r) {
                    /** @var \DOMElement $r */
                    $c['Impuestos']['Retenciones'][] = [
                        'Base'       => $r->getAttribute('Base'),
                        'Impuesto'   => $r->getAttribute('Impuesto'),
                        'TipoFactor' => $r->getAttribute('TipoFactor'),
                        'TasaOCuota' => $r->getAttribute('TasaOCuota'),
                        'Importe'    => $r->getAttribute('Importe'),
                    ];
                }

                $conceptos[] = $c;
            }
            $impuestosGlobales = [
                'TotalImpuestosTrasladados' => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Impuestos/@TotalImpuestosTrasladados)'),
                'TotalImpuestosRetenidos'   => $xp->evaluate('string(//cfdi:Comprobante/cfdi:Impuestos/@TotalImpuestosRetenidos)'),
                'Traslados'   => [],
                'Retenciones' => [],
            ];

            $tgNodes = $xp->query('//cfdi:Comprobante/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
            foreach ($tgNodes as $tg) {
                /** @var \DOMElement $tg */
                $impuestosGlobales['Traslados'][] = [
                    'Impuesto'   => $tg->getAttribute('Impuesto'),
                    'TipoFactor' => $tg->getAttribute('TipoFactor'),
                    'TasaOCuota' => $tg->getAttribute('TasaOCuota'),
                    'Importe'    => $tg->getAttribute('Importe'),
                    'Base'       => $tg->getAttribute('Base'),
                ];
            }
            $rgNodes = $xp->query('//cfdi:Comprobante/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion');
            foreach ($rgNodes as $rg) {
                /** @var \DOMElement $rg */
                $impuestosGlobales['Retenciones'][] = [
                    'Impuesto' => $rg->getAttribute('Impuesto'),
                    'Importe'  => $rg->getAttribute('Importe'),
                ];
            }
            $data['conceptos'] = $conceptos;
            $data['impuestos_globales'] = $impuestosGlobales;
        }
        $data['complemento_pagos20'] = $pagos20;
        $pdfBase64 = base64_encode(file_get_contents($pdfPath));
        $xmlBase64 = base64_encode(file_get_contents($xmlPath));
        $nf6 = fn($v) => number_format((float)str_replace(',', '', ($v ?? 0)), 6, '.', '');
        $nf2 = fn($v) => number_format((float)str_replace(',', '', ($v ?? 0)), 2, '.', '');

        $uuid       = Arr::get($data, 'timbre.uuid');
        $emisorRfc  = Arr::get($data, 'emisor.rfc');
        $receptRfc  = Arr::get($data, 'receptor.rfc');
        $regimen    = Arr::get($data, 'emisor.regimen');
        $folio      = Arr::get($data, 'folio');
        $tipo       = Arr::get($data, 'tipo_de_comprobante');

        $moneda = $tipo === 'P'
            ? (Arr::get($data, 'complemento_pagos20.pagos.0.MonedaP') ?: Arr::get($data, 'moneda', 'MXN'))
            : Arr::get($data, 'moneda', 'MXN');

        $tipo_cambio = $tipo === 'P'
            ? (Arr::get($data, 'complemento_pagos20.pagos.0.TipoCambioP') ?: 1)
            : (Arr::get($data, 'tipo_cambio') ?: 1);

        $monto_raw = $tipo === 'P'
            ? Arr::get($data, 'complemento_pagos20.pagos.0.Monto', 0)
            : Arr::get($data, 'total', 0);
        $monto = $nf2($monto_raw);

        $iso = $tipo === 'P'
            ? (Arr::get($data, 'complemento_pagos20.pagos.0.FechaPago') ?: Arr::get($data, 'fecha', '2025-08-11T00:00:00'))
            : Arr::get($data, 'fecha', '2025-08-11T00:00:00');

        $fecha = Carbon::parse($iso)->timezone('America/Mexico_City')->format('d/m/Y');

        $tipo = Arr::get($data, 'tipo_de_comprobante');
        $clave_prod_serv = Arr::get($data, 'conceptos.0.ClaveProdServ')
            ?: Arr::get($data, 'conceptos.0.clave_prod_serv')
            ?: ($tipo === 'P' ? '84111506' : null);

        $concepto = Arr::get($data, 'conceptos.0.Descripcion')
            ?: Arr::get($data, 'conceptos.0.descripcion')
            ?: ($tipo === 'P' ? 'Pago' : null);

        $traslados = [];

        foreach (($data['complemento_pagos20']['pagos'] ?? []) as $pago) {
            foreach (($pago['impuestosP']['trasladosP'] ?? []) as $t) {
                $traslados[] = [
                    "Base"       => $nf6($t['BaseP']       ?? 0),
                    "Impuesto"   => (string)($t['ImpuestoP']   ?? ''),
                    "TipoFactor" => (string)($t['TipoFactorP'] ?? ''),
                    "TasaOCuota" => $nf6($t['TasaOCuotaP'] ?? 0),
                    "Importe"    => $nf2($t['ImporteP']    ?? 0),
                ];
            }
        }


        if (empty($traslados)) {
            $conceptNodes = $xp->query('//cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto');
            foreach ($conceptNodes as $n) {
                $tNodes = $xp->query('cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado', $n);
                foreach ($tNodes as $tn) {
                    /** @var \DOMElement $tn */
                    $traslados[] = [
                        "Base"       => $nf6($tn->getAttribute('Base')),
                        "Impuesto"   => (string)$tn->getAttribute('Impuesto'),
                        "TipoFactor" => (string)$tn->getAttribute('TipoFactor'),
                        "TasaOCuota" => $nf6($tn->getAttribute('TasaOCuota')),
                        "Importe"    => $nf2($tn->getAttribute('Importe')),
                    ];
                }
            }

            if (empty($traslados)) {
                $tGlobal = $xp->query('//cfdi:Comprobante/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
                foreach ($tGlobal as $tg) {
                    /** @var \DOMElement $tg */
                    $traslados[] = [
                        "Base"       => $nf6($tg->getAttribute('Base')),
                        "Impuesto"   => (string)$tg->getAttribute('Impuesto'),
                        "TipoFactor" => (string)$tg->getAttribute('TipoFactor'),
                        "TasaOCuota" => $nf6($tg->getAttribute('TasaOCuota')),
                        "Importe"    => $nf2($tg->getAttribute('Importe')),
                    ];
                }
            }
        }

        $item = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $purchase_order_id)
            ->first();

        $department_name = $item?->department;
        $class_name = $item?->class;
        $location_name = Str::of($item->location ?? '')
            ->replaceMatches('/^.*:\s*/', '')
            ->squish()
            ->toString();

        $category_name = $item?->categoria;
        $description = $item?->description;

        $departamentos = NetsuiteDepartments::where('name', $department_name)
            ->first();
        $department_id = $departamentos?->external_id ?? 146;

        $clases = NetsuiteClass::where('name', $class_name)
            ->first();
        $class_id = $clases?->external_id ?? 189 ;

        $ubicaciones = NetsuiteLocations::where('name', $location_name)
            ->first();
        $ubicacion_id = $ubicaciones?->external_id ?? 314;

        $categorias = NetsuiteExpenseCategories::where('name', $category_name)
            ->first();
        $catergoria_id = $categorias?->external_id ?? 118;

        $ordenes_compra = SupplierPurchaseOrder::where('id', $purchase_order_id)
            ->first();
        $purchase_order_id = $ordenes_compra?->purchase_order_id;
        $supplier_external_id = $ordenes_compra?->supplier_external_id;

        $suppliers= Supplier::where('external_id', $supplier_external_id)
            ->first();
        $supplier_rfc = $suppliers?->tax;


        $data_netsuite = [
            "idproveedor" => $supplier_external_id,
            "iddoc" => $purchase_order_id,
            "tipo_doc" => "PurchOrd",
            "rfc" => $supplier_rfc,
            "nfactura" => $folio,
            "regimenfiscal" => $regimen,
            "moneda" => $moneda,
            "termino" => "",
            "departamento" => $department_id,
            "clase" => $class_id,
            "operacion" => "",
            "tipocambio" => $tipo_cambio,
            "fecha" => "20/08/2025",
            "ubicacion" => $ubicacion_id,
            "idnetsuite" => "",
            "modo_prueba" => true,
            "uuid" => $uuid,
            "gastos" => [
                [
                    "categoria" => $catergoria_id,
                    "costo" => $monto,
                    "ubicacion" => $ubicacion_id,
                    "departamento" => $department_id,
                    "clase" => $class_id,
                    "concepto" => $description,
                    "claveprodser" => $clave_prod_serv,
                    "Impuestos" => []
                ]
            ],
            "articulos" => [],
            "nota" => $description,
            "generico" => "",
            "xml" => $xmlBase64,
            "pdf" => $pdfBase64
        ];

        $data_netsuite['gastos'][0]['Impuestos']['Traslados']['Traslado'] = $traslados;

        $restletPath = "/restlet.nl?script=5141&deploy=1";
        try {
            $response = $this->netSuiteRestService->request($restletPath, 'POST', $data_netsuite);
            return response()->json(['ok' => true, 'response' => $response,"data" => $data_netsuite]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage(),"data" => $data_netsuite], 500);
        }
    }
}
