<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\NetSuiteRestService;
use App\Models\SupplierPurchaseOrdersReceipt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class SupplierPurchaseOrderReceiptController extends Controller
{
    private NetSuiteRestService $netSuiteRestService;
    public function __construct(NetSuiteRestService $netSuiteRestService)
    {
        $this->netSuiteRestService = $netSuiteRestService;
    }

    public function index()
    {
        $restletPath = "/restlet.nl?script=5136&deploy=1";

        try {
            $raw = $this->netSuiteRestService->request($restletPath, 'GET');
            $payload = is_string($raw)
                ? json_decode($raw, true, 512, JSON_THROW_ON_ERROR)
                : $raw;

            $rows = $payload['data'] ?? [];

            $created = 0; $updated = 0;

            DB::transaction(function () use ($rows, &$created, &$updated) {
                foreach ($rows as $r) {
                    $externalId = (int)($r['internalid'] ?? 0);
                    if (!$externalId) continue;

                    $po = $r['po'] ?? null;
                    if ($po && preg_match('/#([\w\-]+)/', $po, $m)) {
                        $po = $m[1];
                    }

                    $date = null;
                    if (!empty($r['date'])) {
                        try {
                            $date = Carbon::createFromFormat('j/n/Y', str_replace(['-', '.'], '/', $r['date']))->format('Y-m-d');
                        } catch (\Throwable $e) {

                        }
                    }

                    $dataToSave = [
                        'tranid'         => $r['tranid'] ?? null,
                        'date'           => $date,
                        'vendor'         => $r['vendor'] ?? null,
                        'purchase_order' => $po,
                        'status'         => $r['status'] ?? null,
                        'quantity'       => $r['quantity'] ?? null,
                        'url'            => $r['url'] ?? null,
                    ];

                    $model = SupplierPurchaseOrdersReceipt::updateOrCreate(
                        ['external_id' => $externalId],
                        $dataToSave
                    );

                    $model->wasRecentlyCreated ? $created++ : $updated++;
                }
            });

            return response()->json([
                'ok'      => true,
                'created' => $created,
                'updated' => $updated,
                'total'   => $created + $updated,
            ]);
        } catch (\JsonException $e) {
            return response()->json(['ok' => false, 'error' => 'Invalid JSON from Restlet'], 422);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }



    public function create()
    {
        return Inertia::render('Suppliers/PurchaseOrders/Create');
    }

    public function show($id)
    {
        
    }


    public function store(Request $request)
    {
        // El payload llega anidado, pero solo extraemos lo que sí existe en la tabla
        $input = $request->all();

        // Requerimos solo lo mínimo para guardar
        $externalId = (int) data_get($input, 'receipt.id');
        if (!$externalId) {
            return response()->json(['ok' => false, 'error' => 'receipt.id es requerido'], 422);
        }

        // Mapear a columnas reales
        $tranid        = data_get($input, 'receipt.number');                           // varchar(100)
        $dateStr       = data_get($input, 'receipt.date');                             // string -> date
        $vendor        = data_get($input, 'vendor.name');                              // varchar(255)
        $purchaseOrder = data_get($input, 'purchaseOrder.number')                      // varchar(100)
                        ?? data_get($input, 'purchaseOrder.id');
        $status        = data_get($input, 'receipt.status');                           // varchar(100)
        $quantity      = data_get($input, 'totalQuantity');                            // varchar(100) en tu tabla
        $url           = data_get($input, 'receipt.url');                              // varchar(255)

        // Parseo de fecha (acepta 22/8/2025, 22/08/2025, 2025-08-22, etc.)
        $date = null;
        foreach (['d/m/Y', 'j/n/Y', 'Y-m-d'] as $fmt) {
            try { $date = Carbon::createFromFormat($fmt, (string) $dateStr)->toDateString(); break; }
            catch (\Throwable $e) { /* intenta el siguiente formato */ }
        }
        if (!$date) {
            try { $date = Carbon::parse((string) $dateStr)->toDateString(); }
            catch (\Throwable $e) {
                return response()->json(['ok' => false, 'error' => 'Formato de fecha inválido'], 422);
            }
        }

        // Upsert SOLO con columnas de tu tabla
        $payload = [
            'tranid'         => $tranid,
            'date'           => $date,
            'vendor'         => $vendor,
            'purchase_order' => $purchaseOrder,
            'status'         => $status,
            'quantity'       => isset($quantity) ? (string) $quantity : null,
            'url'            => $url,
        ];

        try {
            DB::transaction(function () use ($externalId, $payload) {
                SupplierPurchaseOrdersReceipt::updateOrCreate(
                    ['external_id' => $externalId],
                    $payload
                );
            });

            $model = SupplierPurchaseOrdersReceipt::where('external_id', $externalId)->first();

            return response()->json([
                'ok'   => true,
                'data' => $model,
                'msg'  => 'Recepción guardada.'
            ], 201);

        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }


}
