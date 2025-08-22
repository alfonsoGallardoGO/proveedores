<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\SupplierPurchaseOrderItem;
use App\Models\SupplierPurchaseOrder;

class PdfController extends Controller
{


    public function generateInvoice($id)
    {
        $orders = SupplierPurchaseOrder::findOrFail($id);
        $items = SupplierPurchaseOrderItem::where('supplier_purchase_order_id', $id)->get();

        $data = [
            'orderNumber' => $orders->purchase_order,
            'items' => $items,
        ];

        $pdf = Pdf::loadView('pdfs.purchase_order_invoice', $data);

        return $pdf->stream('orden-de-compra-' . $orders->purchase_order . '.pdf');
    }
}
