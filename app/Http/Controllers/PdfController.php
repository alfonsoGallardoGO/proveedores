<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use App\Models\SupplierPurchaseOrderItem;
use App\Models\SupplierPurchaseOrder;
use Milon\Barcode\DNS1D;

class PdfController extends Controller
{


    public function generateInvoice(Request $request, $id)
    {
        
        $orderId = $id;
        $supplierId = $request->input('supplier_id');
        $supplierName = $request->input('supplier_name');

        // dd($orderId, $supplierId, $supplierName);

        $supplier = Supplier::find($supplierId);
        $orders = SupplierPurchaseOrder::findOrFail($orderId);
        $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $supplierId)->get();

        if (!$supplier) {
            abort(404, 'Proveedor no encontrado.');
        }

        $totalItems = $items->sum('amount');
        // dd($totalItems);

        $logoPath = base_path('resources/views/pdfs/Logo.png');
        if (!file_exists($logoPath)) {
            $logoBase64 = null;
        } else {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $barcodeGenerator = new DNS1D();
        $barcodeHtml = $barcodeGenerator->getBarcodeHTML($orders->purchase_order, 'C128');


        $data = [
            'orders' => $orders,
            'items' => $items,
            'supplier' => $supplier,
            'supplierName' => $supplierName,
            'logoBase64' => $logoBase64,
            'barcode' => $barcodeHtml,
            'totalItems' => $totalItems,
        ];

        // dd($data);
        
        // $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        // $pdf = Pdf::loadView('pdfs.purchase_order_invoice', $data);
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdfs.purchase_order_invoice', $data);

        return $pdf->stream('orden-de-compra-' . $orders->purchase_order . '.pdf');
        // return view('pdfs.purchase_order_invoice', $data);
    }
}
