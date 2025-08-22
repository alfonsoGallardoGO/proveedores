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

        return $request;
        $data = $request->validate([
            'external_id'    => 'nullable|integer',
            'internalid'     => 'nullable|integer',     // fallback
            'tranid'         => 'nullable|string|max:100',
            'date'           => 'required|string',      // la convertimos abajo
            'vendor'         => 'nullable|string|max:255',
            'purchase_order' => 'nullable|string|max:100',
            'po'             => 'nullable|string|max:100', // fallback
            'status'         => 'nullable|string|max:100',
            'quantity'       => 'nullable',
            'url'            => 'nullable|url|max:255',
        ]);

        // 2) Tomar external_id y purchase_order con fallback
        $externalId    = $data['external_id'] ?? $data['internalid'] ?? null;
        $purchaseOrder = $data['purchase_order'] ?? $data['po'] ?? null;

        if (is_null($externalId)) {
            return response()->json([
                'ok' => false,
                'error' => 'external_id (o internalid) es requerido'
            ], 422);
        }

        
        $dateStr = $data['date'];
        $date    = null;
        try {
            
            $date = Carbon::createFromFormat('d/m/Y', $dateStr)->toDateString();
        } catch (\Throwable $e) {
            try {
                $date = Carbon::parse($dateStr)->toDateString();
            } catch (\Throwable $e2) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Formato de fecha inválido'
                ], 422);
            }
        }
        try {
            DB::transaction(function () use ($externalId, $data, $purchaseOrder, $date) {
                SupplierPurchaseOrdersReceipt::updateOrCreate(
                    ['external_id' => (int) $externalId],
                    [
                        'tranid'         => $data['tranid'] ?? null,
                        'date'           => $date,                // YYYY-MM-DD
                        'vendor'         => $data['vendor'] ?? null,
                        'purchase_order' => $purchaseOrder,
                        'status'         => $data['status'] ?? null,
                        'quantity'       => isset($data['quantity']) ? (string)$data['quantity'] : null,
                        'url'            => $data['url'] ?? null,
                        'updated_at'     => now(),
                    ]
                );
            });

            $model = SupplierPurchaseOrdersReceipt::where('external_id', $externalId)->first();

            return response()->json([
                'ok'    => true,
                'data'  => $model,
                'msg'   => 'recepcion guardado correctamente (upsert).'
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
