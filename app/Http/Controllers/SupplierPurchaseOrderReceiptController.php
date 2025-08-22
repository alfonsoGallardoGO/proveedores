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
        
    }

}
